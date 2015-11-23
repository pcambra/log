<?php

/**
 * @file
 * Contains \Drupal\log\Entity\Form\LogForm.
 */

namespace Drupal\log\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Form controller for Log edit forms.
 *
 * @ingroup log
 */
class LogForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\log\Entity\Log */
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->langcode->value,
      '#languages' => Language::STATE_ALL,
    );

    $form['revision'] = array(
      '#title' => $this->t('Revision'),
      '#type' => 'checkbox',
      '#default_value' => TRUE,
    );

    $form = parent::form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * Updates the node object by processing the submitted values.
   *
   * This function can be called by a "Next" button of a wizard to update the
   * form state's entity with the current step's values before proceeding to the
   * next step.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Build the node object from the submitted values.
    parent::submitForm($form, $form_state);
    $entity = $this->entity;

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('revision') && $form_state->getValue('revision') != FALSE) {
      $entity->setNewRevision();
      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionAuthorId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Log.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Log.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.log.canonical', ['log' => $entity->id()]);
  }

}
