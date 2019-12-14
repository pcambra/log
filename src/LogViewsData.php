<?php

namespace Drupal\log;

use Drupal\views\EntityViewsData;

/**
 * Provides views data for the file entity type.
 */
class LogViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['log']['log_bulk_form'] = array(
      'title' => t('Log bulk operations form'),
      'help' => t('Add a form element that lets you run operations on multiple log entities.'),
      'field' => array(
        'id' => 'log_bulk_form',
      ),
    );

    return $data;
  }

}
