<?php

/**
 * @file
 * Log classes.
 */

/**
 * Log class.
 */
class Log extends Entity {
  protected function defaultLabel() {
    return $this->name;
  }

  protected function defaultUri() {
    return array('path' => 'log/' . $this->identifier());
  }
}

/**
 * Log controller class.
 */
class LogController extends EntityAPIController {

  public function create(array $values = array()) {
    global $user;
    $values += array(
      'lid' => NULL,
      'timestamp' => REQUEST_TIME,
      'name' => '',
      'type' => '',
      'uid' => $user->uid,
    );
    return parent::create($values);
  }
}

/**
 * Log Type class.
 */
class LogType extends Entity {
  public $type;
  public $label;
  public $weight = 0;

  public function __construct($values = array()) {
    parent::__construct($values, 'log_type');
  }

  function isLocked() {
    return isset($this->status) && empty($this->is_new) && (($this->status & ENTITY_IN_CODE) || ($this->status & ENTITY_FIXED));
  }
}

/**
 * Log type controller class.
 */
class LogTypeController extends EntityAPIControllerExportable {
  public function create(array $values = array()) {
    $values += array(
      'id' => NULL,
      'type' => '',
      'label' => '',
    );
    return parent::create($values);
  }
}

/**
 * UI controller for Log Type.
 */
class LogTypeUIController extends EntityDefaultUIController {
  /**
   * Overrides hook_menu() defaults.
   */
  public function hook_menu() {
    $items = parent::hook_menu();
    $items[$this->path]['description'] = 'Manage log types.';
    return $items;
  }
}
