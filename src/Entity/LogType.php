<?php

namespace Drupal\log\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\log\LogTypeInterface;

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
 *       "delete" = "Drupal\log\Form\LogTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\log\LogTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "log_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "log",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/log_type/{log_type}",
 *     "add-form" = "/admin/structure/log_type/add",
 *     "edit-form" = "/admin/structure/log_type/{log_type}/edit",
 *     "delete-form" = "/admin/structure/log_type/{log_type}/delete",
 *     "collection" = "/admin/structure/log_type"
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

}
