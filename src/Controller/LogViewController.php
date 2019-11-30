<?php

namespace Drupal\log\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Controller\EntityViewController;

/**
 * Defines a controller to render a single log.
 */
class LogViewController extends EntityViewController {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $log, $view_mode = 'full') {
    $build = parent::view($log, $view_mode);

    foreach ($log->uriRelationships() as $rel) {
      // Set the log path as the canonical URL to prevent duplicate content.
      $build['#attached']['html_head_link'][] = [
        [
          'rel' => $rel,
          'href' => $log->toUrl($rel),
        ],
        TRUE,
      ];

      if ($rel == 'canonical') {
        // Set the non-aliased canonical path as a default shortlink.
        $build['#attached']['html_head_link'][] = [
          [
            'rel' => 'shortlink',
            'href' => $log->toUrl($rel, ['alias' => TRUE]),
          ],
          TRUE,
        ];
      }
    }

    return $build;
  }

}
