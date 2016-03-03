<?php

namespace Drupal\log\Tests;

/**
 * Tests the Log CRUD.
 *
 * @group log
 */
class LogCRUDTest extends LogTestBase {

  /**
   * Create Log entry.
   *
   * @todo That should be Kernel Test. Test interface here.
   */
  public function testCreateLog() {
    $log = $this->createLogEntity([
      'name' => $name = $this->randomString(),
      'user_id' => $user_id = $this->loggedInUser->id(),
      'created' => REQUEST_TIME,
      'type' => $type = 'default',
      'done' => $done = TRUE,
    ]);
    $log->save();

    $this->assertEqual($name, $log->getName());
    $this->assertEqual($user_id, $log->getOwnerId());
    $this->assertEqual(REQUEST_TIME, $log->getCreatedTime());
    $this->assertEqual($type, $log->getTypeName());
  }
}
