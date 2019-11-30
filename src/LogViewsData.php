<?php

namespace Drupal\log;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Log entities.
 */
class LogViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['log']['log_bulk_form'] = [
      'title' => t('Log bulk operations form'),
      'help' => t('Add a form element that lets you run operations on multiple log entities.'),
      'field' => [
        'id' => 'log_bulk_form',
      ],
    ];

    return $data;
  }

}
