<?php

/**
 * @file
 * Contains \Drupal\log\Entity\LogType.
 */

namespace Drupal\log\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\log\LogTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Log type entity.
 *
 * @ConfigEntityType(
 *   id = "log_type",
 *   label = @Translation("Log types"),
 *   handlers = {
 *     "access" = "Drupal\log\LogTypeAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\log\Form\LogTypeForm",
 *       "edit" = "Drupal\log\Form\LogTypeForm",
 *       "delete" = "Drupal\log\Form\LogTypeDeleteForm"
 *     },
 *     "list_builder" = "Drupal\log\LogTypeListBuilder",
 *   },
 *   admin_permission = "administer site configuration",
 *   config_prefix = "type",
 *   bundle_of = "log",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/log_type/{log_type}",
 *     "edit-form" = "/admin/structure/log_type/{log_type}/edit",
 *     "delete-form" = "/admin/structure/log_type/{log_type}/delete",
 *     "collection" = "/admin/structure/log_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label"
 *   }
 * )
 */
class LogType extends ConfigEntityBundleBase implements LogTypeInterface {

  /**
   * The Log type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Log type label.
   *
   * @var string
   */
  protected $label;


  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if ($update && $this->getOriginalId() != $this->id()) {
      $update_count = node_type_update_nodes($this->getOriginalId(), $this->id());
      if ($update_count) {
        drupal_set_message(\Drupal::translation()->formatPlural($update_count,
          'Changed the log type of 1 post from %old-type to %type.',
          'Changed the log type of @count posts from %old-type to %type.',
          array(
            '%old-type' => $this->getOriginalId(),
            '%type' => $this->id(),
          )));
      }
    }
    if ($update) {
      // Clear the cached field definitions as some settings affect the field
      // definitions.
      $this->entityManager()->clearCachedFieldDefinitions();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Clear the node type cache to reflect the removal.
    $storage->resetCache(array_keys($entities));
  }
}
