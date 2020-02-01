<?php

namespace Drupal\log\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Log entities.
 *
 * @ingroup log
 */
class LogForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $this->messenger()->addMessage($this->t('Saved the %label log.', ['%label' => $this->entity->label()]));
    $account = $this->currentUser();
    if ($account->hasPermission('access log overview')) {
      $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    }
    else {
      $form_state->setRedirectUrl($this->entity->toUrl());
    }
  }

}
