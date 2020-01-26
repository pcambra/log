<?php

namespace Drupal\Tests\log\Kernel\Handler;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\log\Entity\Log;
use Drupal\Tests\views\Kernel\ViewsKernelTestBase;
use Drupal\views\Tests\ViewTestData;
use Drupal\views\Views;

/**
 * Tests for Drupal\log\Plugin\views\sort\LogTimestampIdSort handler.
 *
 * @group views
 */
class SortTimestampIdTest extends ViewsKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['log', 'log_test', 'datetime', 'state_machine'];

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['log_test_view'];

  /**
   * ASC-DESC expected result.
   *
   * @var array
   */
  protected $expectedResultASCDESC = [];

  /**
   * ASC-ASC expected result.
   *
   * @var array
   */
  protected $expectedResultASCASC = [];

  /**
   * DESC-ASC expected result.
   *
   * @var array
   */
  protected $expectedResultDESCASC = [];

  /**
   * DESC-DESC expected result.
   *
   * @var array
   */
  protected $expectedResultDESCDESC = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp($import_test_views = TRUE) {
    parent::setUp();

    $this->installEntitySchema('log');
    $this->installConfig(['log', 'log_test']);

    ViewTestData::createTestViews(get_class($this), ['log_test']);

    // Establish two different timestamps so the sort is meaningful.
    $first_timestamp = DrupalDateTime::createFromTimestamp(376185600, DateTimeItemInterface::STORAGE_TIMEZONE)->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    $second_timestamp = DrupalDateTime::createFromTimestamp(386121600, DateTimeItemInterface::STORAGE_TIMEZONE)->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    // Three entities is the minimum amount to test two with the same timestamp
    // and different ID and one with unique timestamp.
    $first_entity = $this->createLogEntity([
      'name' => 'First',
      'timestamp' => $first_timestamp,
    ]);
    $second_entity = $this->createLogEntity([
      'name' => 'Second',
      'timestamp' => $first_timestamp,
    ]);
    $third_entity = $this->createLogEntity([
      'name' => 'Third',
      'timestamp' => $second_timestamp,
    ]);

    // Fill the expected results for the four possible combinations.
    $this->expectedResultASCDESC = [
      ['name' => $second_entity->get('name')->value, 'id' => $second_entity->id()],
      ['name' => $first_entity->get('name')->value, 'id' => $first_entity->id()],
      ['name' => $third_entity->get('name')->value, 'id' => $third_entity->id()],
    ];
    $this->expectedResultASCASC = [
      ['name' => $first_entity->get('name')->value, 'id' => $first_entity->id()],
      ['name' => $second_entity->get('name')->value, 'id' => $second_entity->id()],
      ['name' => $third_entity->get('name')->value, 'id' => $third_entity->id()],
    ];
    $this->expectedResultDESCASC = [
      ['name' => $third_entity->get('name')->value, 'id' => $third_entity->id()],
      ['name' => $first_entity->get('name')->value, 'id' => $first_entity->id()],
      ['name' => $second_entity->get('name')->value, 'id' => $second_entity->id()],
    ];
    $this->expectedResultDESCDESC = [
      ['name' => $third_entity->get('name')->value, 'id' => $third_entity->id()],
      ['name' => $second_entity->get('name')->value, 'id' => $second_entity->id()],
      ['name' => $first_entity->get('name')->value, 'id' => $first_entity->id()],
    ];
  }

  /**
   * Creates a log entity.
   *
   * @param array $values
   *   Array of values to feed the entity.
   *
   * @return \Drupal\log\Entity\LogInterface
   *   The log entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createLogEntity(array $values = []) {
    /** @var \Drupal\log\Entity\LogInterface $entity */
    $entity = Log::create($values + [
      'name' => $this->randomMachineName(),
      'type' => 'default',
    ]);
    $entity->save();
    return $entity;
  }

  /**
   * Tests the default sorting (Timestamp DESC, ID ASC).
   */
  public function testLogTimestampIdDefaultSort() {
    $view = Views::getView('log_test_view');
    $view->setDisplay();
    $this->executeView($view);

    $this->assertEqual(3, count($view->result), 'The number of returned rows match.');
    $this->assertIdenticalResultset($view, $this->expectedResultDESCASC, [
      'name' => 'name',
      'id' => 'id',
    ], 'Default sort displays as expected');
    $view->destroy();
    unset($view);
  }

  /**
   * Tests the sorting: Timestamp ASC, ID DESC.
   */
  public function testLogTimestampIdAscDescSort() {
    $view = Views::getView('log_test_view');
    $view->setDisplay();

    // Change the ordering to be Timestamp ASC, id DESC.
    $view->displayHandlers->get('default')->overrideOption('sorts', [
      'log_timestamp_id' => [
        'id' => 'log_timestamp_id',
        'table' => 'log_field_data',
        'field' => 'log_timestamp_id',
        'relationship' => 'none',
        'timestamp_order' => 'ASC',
        'id_order' => 'DESC',
      ],
    ]);

    $this->executeView($view);

    $this->assertEqual(3, count($view->result), 'The number of returned rows match.');
    $this->assertIdenticalResultset($view, $this->expectedResultASCDESC, [
      'name' => 'name',
      'id' => 'id',
    ], 'ASC DESC sort displays as expected');
    $view->destroy();
    unset($view);
  }

  /**
   * Tests the sorting: Timestamp ASC, ID ASC.
   */
  public function testLogTimestampIdAscAscSort() {
    $view = Views::getView('log_test_view');
    $view->setDisplay();

    // Change the ordering to be Timestamp ASC, id DESC.
    $view->displayHandlers->get('default')->overrideOption('sorts', [
      'log_timestamp_id' => [
        'id' => 'log_timestamp_id',
        'table' => 'log_field_data',
        'field' => 'log_timestamp_id',
        'relationship' => 'none',
        'timestamp_order' => 'ASC',
        'id_order' => 'ASC',
      ],
    ]);

    $this->executeView($view);

    $this->assertEqual(3, count($view->result), 'The number of returned rows match.');
    $this->assertIdenticalResultset($view, $this->expectedResultASCASC, [
      'name' => 'name',
      'id' => 'id',
    ], 'ASC ASC sort displays as expected');
    $view->destroy();
    unset($view);
  }

  /**
   * Tests the sorting: Timestamp DESC, ID DESC.
   */
  public function testLogTimestampIdDescDescSort() {
    $view = Views::getView('log_test_view');
    $view->setDisplay();

    // Change the ordering to be Timestamp ASC, id DESC.
    $view->displayHandlers->get('default')->overrideOption('sorts', [
      'log_timestamp_id' => [
        'id' => 'log_timestamp_id',
        'table' => 'log_field_data',
        'field' => 'log_timestamp_id',
        'relationship' => 'none',
        'timestamp_order' => 'DESC',
        'id_order' => 'DESC',
      ],
    ]);

    $this->executeView($view);

    $this->assertEqual(3, count($view->result), 'The number of returned rows match.');
    $this->assertIdenticalResultset($view, $this->expectedResultDESCDESC, [
      'name' => 'name',
      'id' => 'id',
    ], 'DESC DESC sort displays as expected');
    $view->destroy();
    unset($view);
  }

}
