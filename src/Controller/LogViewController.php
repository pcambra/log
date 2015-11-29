<?php

/**
 * @file
 * Contains \Drupal\log\Controller\LogViewController.
 */

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
  public function view(EntityInterface $log, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($log, $view_mode, $langcode);

    foreach ($log->uriRelationships() as $rel) {
      // Set the log path as the canonical URL to prevent duplicate content.
      $build['#attached']['html_head_link'][] = array(
        array(
          'rel' => $rel,
          'href' => $log->url($rel),
        ),
        TRUE,
      );

      if ($rel == 'canonical') {
        // Set the non-aliased canonical path as a default shortlink.
        $build['#attached']['html_head_link'][] = array(
          array(
            'rel' => 'shortlink',
            'href' => $log->url($rel, array('alias' => TRUE)),
          ),
          TRUE,
        );
      }
    }

    return $build;
  }

  /**
   * The _title_callback for the page that renders a single log.
   *
   * @param \Drupal\Core\Entity\EntityInterface $log
   *   The current log.
   *
   * @return string
   *   The page title.
   */
  public function title(EntityInterface $log) {
    return $log->label();
  }

}
