<?php

namespace Drupal\log\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Log type entities.
 */
interface LogTypeInterface extends ConfigEntityInterface {

  /**
   * Returns the description for a log type.
   *
   * @return string
   *   The log type description.
   */
  public function getDescription();

  /**
   * Returns the name pattern for a log type.
   *
   * @return string
   *   The log type name pattern.
   */
  public function getNamePattern();

}
