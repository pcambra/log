<?php

/**
 * @file
 * Contains \Drupal\log\Entity\Log.
 */

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

    $data['log']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Log'),
      'help' => $this->t('The Log ID.'),
    );

    return $data;
  }

}
