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

    // @TODO
    // Left intentionally blank for integrating reverse views relationships in
    // the future.
    // @see https://www.drupal.org/project/drupal/issues/2706431

    // Custom sort handler that sorts by timestamp and ID.
    $data['log_field_data']['log_timestamp_id'] = [
      'group' => 'Log',
      'title' => t('Log timestamp and Id sort'),
      'title short' => t('Log timestamp and Id sort'),
      'help' => $this->t('Logs are better off sorted by timestamp and id'),
      'sort' => [
        'id' => 'log_timestamp_id',
      ],
    ];

    return $data;
  }

}
