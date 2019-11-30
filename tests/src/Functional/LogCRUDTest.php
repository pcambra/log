<?php

namespace Drupal\Tests\log\Functional;

use Drupal\log\Entity\Log;

/**
 * Tests the Log CRUD.
 *
 * @group log
 */
class LogCRUDTest extends LogTestBase {

  /**
   * Fields are displayed correctly.
   */
  public function testFieldsVisibility() {
    $this->drupalGet('log/add/default');
    $this->assertResponse('200');
    $assert_session = $this->assertSession();
    $assert_session->fieldExists('name[0][value]');
    $assert_session->fieldExists('timestamp[0][value][date]');
    $assert_session->fieldExists('timestamp[0][value][time]');
    $assert_session->fieldExists('done[value]');
    $assert_session->fieldExists('revision');
    $assert_session->fieldExists('user_id[0][target_id]');
    $assert_session->fieldExists('created[0][value][date]');
    $assert_session->fieldExists('created[0][value][time]');
  }

  /**
   * Create Log entity.
   */
  public function testCreateLog() {
    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];

    $this->drupalPostForm('log/add/default', $edit, t('Save'));

    $result = \Drupal::entityQuery('log')
      ->condition('name', $edit['name[0][value]'])
      ->range(0, 1)
      ->execute();
    $log_id = reset($result);
    $log = Log::load($log_id);
    $this->assertNotNull($log, 'Log has been created.');

    $this->assertRaw(t('Created the %label Log.', ['%label' => $edit['name[0][value]']]));
    $this->assertText($edit['name[0][value]']);
    $this->assertText($this->loggedInUser->getDisplayName());
  }

  /**
   * Display log entity.
   */
  public function testViewLog() {
    $edit = [
      'name' => $this->randomMachineName(),
      'created' => \Drupal::time()->getRequestTime(),
      'done' => TRUE,
    ];
    $log = $this->createLogEntity($edit);
    $log->save();

    $this->drupalGet($log->toUrl('canonical'));
    $this->assertResponse(200);

    $this->assertText($edit['name']);
    $this->assertRaw(\Drupal::service('date.formatter')->format(\Drupal::time()->getRequestTime()));
    $this->assertText($this->loggedInUser->getDisplayName());
  }

  /**
   * Edit log entity.
   */
  public function testEditLog() {
    $log = $this->createLogEntity();
    $log->save();

    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm($log->toUrl('edit-form'), $edit, t('Save'));

    $this->assertRaw(t('Saved the %label Log.', ['%label' => $edit['name[0][value]']]));
    $this->assertText($edit['name[0][value]']);
  }

  /**
   * Delete log entity.
   */
  public function testDeleteLog() {
    $log = $this->createLogEntity();
    $log->save();

    $label = $log->getName();
    $log_id = $log->id();

    $this->drupalPostForm($log->toUrl('delete-form'), [], t('Delete'));
    $this->assertRaw(t('The @entity-type %label has been deleted.', [
      '@entity-type' => $log->getEntityType()->getLowercaseLabel(),
      '%label' => $label,
    ]));
    $this->assertNull(Log::load($log_id));
  }

}
