<?php

namespace Drupal\log\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\feeds\FeedStorageInterface;
use Drupal\log\Entity\LogType;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a log deletion confirmation form.
 */
class DeleteMultiple extends ConfirmFormBase {

  /**
   * The array of logs to delete.
   *
   * @var string[][]
   */
  protected $logInfo = [];

  /**
   * The tempstore factory.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * The entity storage class.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a DeleteMultiple form object.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore factory.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, EntityStorageInterface $storage) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private'),
      $container->get('entity_type.manager')->getStorage('log')
    );
  }

  /**
   * Checks access to the form.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account to check access for.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function access(AccountInterface $account) {
    /** @var \Drupal\log\LogTypeInterface $type */
    foreach (LogType::loadMultiple() as $type) {
      // If the user has either access to deleting own log entities, or access
      // to deleting all entities in at least one type, they should be able to
      // access the bulk confirm form. If they for some reason try to go there
      // to delete one they don't have access to, the entity access will forbid
      // it anyway.
      if ($account->hasPermission('delete own ' . $type->id() . ' log entities') || $account->hasPermission('delete any ' . $type->id() . ' log entities')) {
        return AccessResult::allowed();
      }
    }
    // In addition we grant access if the user can administer log entities.
    if ($account->hasPermission('administer logs')) {
      return AccessResult::allowed();
    }
    // If none of the above, the user is not allowed access.
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'log_multiple_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->formatPlural(count($this->logInfo), 'Are you sure you want to delete this item?', 'Are you sure you want to delete these items?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {

    // Return the URL of the front page.
    return Url::fromRoute('<front>');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->logInfo = $this->tempStoreFactory->get('log_multiple_delete_confirm')->get(\Drupal::currentUser()->id());
    if (empty($this->logInfo)) {
      return new RedirectResponse($this->getCancelUrl()->setAbsolute()->toString());
    }
    /** @var \Drupal\log\LogInterface[] $logs */
    $logs = $this->storage->loadMultiple(array_keys($this->logInfo));

    $items = [];
    foreach ($this->logInfo as $id => $langcodes) {
      foreach ($langcodes as $langcode) {
        $log = $logs[$id]->getTranslation($langcode);
        $key = $id . ':' . $langcode;
        $default_key = $id . ':' . $log->getUntranslated()->language()->getId();

        // If we have a translated entity we build a nested list of translations
        // that will be deleted.
        $languages = $log->getTranslationLanguages();
        if (count($languages) > 1 && $log->isDefaultTranslation()) {
          $names = [];
          foreach ($languages as $translation_langcode => $language) {
            $names[] = $language->getName();
            unset($items[$id . ':' . $translation_langcode]);
          }
          $items[$default_key] = [
            'label' => [
              '#markup' => $this->t('@label (Original translation) - <em>The following content translations will be deleted:</em>', ['@label' => $log->label()]),
            ],
            'deleted_translations' => [
              '#theme' => 'item_list',
              '#items' => $names,
            ],
          ];
        }
        elseif (!isset($items[$default_key])) {
          $items[$key] = $log->label();
        }
      }
    }

    $form['logs'] = [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('confirm') && !empty($this->logInfo)) {
      $total_count = 0;
      $delete_logs = [];
      /** @var \Drupal\Core\Entity\ContentEntityInterface[][] $delete_translations */
      $delete_translations = [];
      /** @var \Drupal\log\LogInterface[] $logs */
      $logs = $this->storage->loadMultiple(array_keys($this->logInfo));

      foreach ($this->logInfo as $id => $langcodes) {
        foreach ($langcodes as $langcode) {
          $log = $logs[$id]->getTranslation($langcode);
          if ($log->isDefaultTranslation()) {
            $delete_logs[$id] = $log;
            unset($delete_translations[$id]);
            $total_count += count($log->getTranslationLanguages());
          }
          elseif (!isset($delete_logs[$id])) {
            $delete_translations[$id][] = $log;
          }
        }
      }

      if ($delete_logs) {
        $this->storage->delete($delete_logs);
        $this->logger('log')->notice('Deleted @count posts.', ['@count' => count($delete_logs)]);
      }

      if ($delete_translations) {
        $count = 0;
        foreach ($delete_translations as $id => $translations) {
          $log = $logs[$id]->getUntranslated();
          foreach ($translations as $translation) {
            $log->removeTranslation($translation->language()->getId());
          }
          $log->save();
          $count += count($translations);
        }
        if ($count) {
          $total_count += $count;
          $this->logger('log')->notice('Deleted @count content translations.', ['@count' => $count]);
        }
      }

      if ($total_count) {
        $this->messenger()->addMessage($this->formatPlural($total_count, 'Deleted 1 post.', 'Deleted @count posts.'));
      }

      $this->tempStoreFactory->get('log_multiple_delete_confirm')->delete(\Drupal::currentUser()->id());
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
