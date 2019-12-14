<?php

namespace Drupal\Tests\log\Functional;

use Drupal\log\Entity\Log;

/**
 * Tests the Log name pattern.
 *
 * @group log
 */
class LogNamePatternTest extends LogTestBase {

  /**
   * Tests creating a log entity without name.
   */
  public function testCreateLogWithoutName() {
    $this->drupalPostForm('log/add/name_pattern', [], t('Save'));

    $result = \Drupal::entityTypeManager()
      ->getStorage('log')
      ->getQuery()
      ->range(0, 1)
      ->execute();
    $log_id = reset($result);
    $log = Log::load($log_id);
    $this->assertEquals($log->label(), $log_id, 'Log name is the pattern and not the name.');

    $this->drupalGet($log->toUrl('canonical'));
    $this->assertResponse(200);
    $this->assertText($log_id);
  }

  /**
   * Tests creating a log entity with name.
   */
  public function testCreateLogWithName() {
    $name = $this->randomMachineName();
    $edit = [
      'name[0][value]' => $name,
    ];

    $this->drupalPostForm('log/add/name_pattern', $edit, t('Save'));

    $result = \Drupal::entityTypeManager()
      ->getStorage('log')
      ->getQuery()
      ->range(0, 1)
      ->execute();
    $log_id = reset($result);
    $log = Log::load($log_id);
    $this->assertEquals($log->get('name')->value, $name, 'Log name is the pattern and not the name.');

    $this->drupalGet($log->toUrl('canonical'));
    $this->assertResponse(200);
    $this->assertText($name);
  }

  /**
   * Edit log entity.
   */
  public function testEditLog() {
    $log = $this->createLogEntity(['type' => 'name_pattern']);
    $log->save();

    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm($log->toUrl('edit-form'), $edit, t('Save'));

    $this->assertText($edit['name[0][value]']);
  }

}
