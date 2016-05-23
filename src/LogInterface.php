<?php

namespace Drupal\log;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Log entities.
 *
 * @ingroup log
 */
interface LogInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Log type.
   *
   * @return string
   *   The Log type.
   */
  public function getType();

  /**
   * Gets the Log name.
   *
   * @return string
   *   Name of the Log.
   */
  public function getName();

  /**
   * Sets the Log name.
   *
   * @param string $name
   *   The Log name.
   *
   * @return \Drupal\log\LogInterface
   *   The called Log entity.
   */
  public function setName($name);

  /**
   * Gets the Log creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Log.
   */
  public function getCreatedTime();

  /**
   * Sets the Log creation timestamp.
   *
   * @param int $timestamp
   *   The Log creation timestamp.
   *
   * @return \Drupal\log\LogInterface
   *   The called Log entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Log published status indicator.
   *
   * Unpublished Log are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Log is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Log.
   *
   * @param bool $published
   *   TRUE to set this Log to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\log\LogInterface
   *   The called Log entity.
   */
  public function setPublished($published);

}
