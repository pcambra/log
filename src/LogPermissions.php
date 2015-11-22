<?php

/**
 * @file
 * Contains \Drupal\log\LogPermissions.
 */

namespace Drupal\log;

use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\log\Entity\LogType;

/**
 * Provides dynamic permissions for logs of different types.
 */
class LogPermissions {

  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * Returns an array of log type permissions.
   *
   * @return array
   *   The log type permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function logTypePermissions() {
    $perms = array();
    // Generate log permissions for all log types.
    foreach (LogType::loadMultiple() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * Returns a list of log permissions for a given log type.
   *
   * @param \Drupal\log\Entity\LogType $type
   *   The log type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(LogType $type) {
    $type_id = $type->id();
    $type_params = array('%type_name' => $type->label());

    return array(
      "create $type_id log" => array(
        'title' => $this->t('%type_name: Create new log', $type_params),
      ),
      "edit any $type_id log" => array(
        'title' => $this->t('%type_name: Edit any log', $type_params),
      ),
      "delete any $type_id log" => array(
        'title' => $this->t('%type_name: Delete any log', $type_params),
      ),
      "view $type_id revisions" => array(
        'title' => $this->t('%type_name: View revisions', $type_params),
      ),
      "revert $type_id revisions" => array(
        'title' => $this->t('%type_name: Revert revisions', $type_params),
        'description' => t('Role requires permission <em>view revisions</em> and <em>edit rights</em> for logs in question, or <em>administer logs</em>.'),
      ),
      "delete $type_id revisions" => array(
        'title' => $this->t('%type_name: Delete revisions', $type_params),
        'description' => $this->t('Role requires permission to <em>view revisions</em> and <em>delete rights</em> for logs in question, or <em>administer logs</em>.'),
      ),
    );
  }

}
