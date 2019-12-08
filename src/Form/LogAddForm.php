<?php

namespace Drupal\log\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Log add forms.
 *
 * @ingroup log
 */
class LogAddForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    /** @var \Drupal\log\Entity\LogInterface $entity */
    $entity = $this->entity;
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    $this->messenger()->addMessage($this->t('Log entity has been saved'));
  }

}
