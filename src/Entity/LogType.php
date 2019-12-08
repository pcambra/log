<?php

namespace Drupal\log\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the Log type entity.
 *
 * @ConfigEntityType(
 *   id = "log_type",
 *   label = @Translation("Log type"),
 *   handlers = {
 *     "list_builder" = "Drupal\log\LogTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\log\Form\LogTypeForm",
 *       "edit" = "Drupal\log\Form\LogTypeForm",
 *       "delete" = "\Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer log types",
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
 *     "label",
 *     "description",
 *     "name_pattern",
 *     "name_edit",
 *     "done",
 *     "new_revision",
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
   * A brief description of this log type.
   *
   * @var string
   */
  protected $description;

  /**
   * Pattern for auto-generating the log name, using tokens.
   *
   * @var string
   */
  protected $name_pattern;

  /**
   * Log name is user editable.
   *
   * @var bool
   */
  protected $name_edit = FALSE;

  /**
   * Automatically mark logs of this type as done.
   *
   * @var bool
   */
  protected $done = FALSE;

  /**
   * Default value of the 'Create new revision' checkbox of this log type.
   *
   * @var bool
   */
  protected $new_revision = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getNamePattern() {
    return $this->name_pattern;
  }

  /**
   * {@inheritdoc}
   */
  public function isNameEditable() {
    return $this->name_edit;
  }

  /**
   * {@inheritdoc}
   */
  public function isAutomaticallyDone() {
    return $this->done;
  }

  /**
   * {@inheritdoc}
   */
  public function isNewRevision() {
    return $this->new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // If the log type id changed, update all existing logs of that type.
    if ($update && $this->getOriginalId() != $this->id()) {
      $update_count = \Drupal::entityTypeManager()->getStorage('log')->updateType($this->getOriginalId(), $this->id());
      if ($update_count) {
        \Drupal::messenger()->addMessage(\Drupal::translation()->formatPlural($update_count,
          'Changed the log type of 1 post from %old-type to %type.',
          'Changed the log type of @count posts from %old-type to %type.',
          [
            '%old-type' => $this->getOriginalId(),
            '%type' => $this->id(),
          ]));
      }
    }
    if ($update) {
      // Clear the cached field definitions as some settings affect the field
      // definitions.
      \Drupal::entityTypeManager()->clearCachedDefinitions();
      \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Clear the log type cache to reflect the removal.
    $storage->resetCache(array_keys($entities));
  }

}
