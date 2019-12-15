<?php

namespace Drupal\Tests\log\Kernel;

use Drupal\log\Entity\Log;
use Drupal\log\Entity\LogType;
use Drupal\Tests\token\Kernel\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Test the book tokens.
 *
 * @group token
 */
class LogTokenTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['log', 'entity', 'user'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('log');
    $this->installConfig(['log']);
    $this->installSchema('system', ['sequences']);
  }

  /**
   * Tests the log tokens.
   */
  public function testLogTokens() {
    $account = $this->createUser();
    $log_type = LogType::create([
      'id' => 'default',
      'label' => $this->randomString(),
    ]);
    $log_type->save();

    /** @var \Drupal\log\Entity\LogInterface $log */
    $log = Log::create([
      'name' => $this->randomString(),
      'timestamp' => 376185600,
      'created' => 386121600,
      'changed' => 1353024000,
      'uid' => $account->id(),
      'type' => $log_type->id(),
    ]);
    $log->save();

    $url_options = ['absolute' => TRUE];
    $tokens = [
      'id' => $log->id(),
      'name' => $log->get('name')->value,
      'revision_id' => $log->getRevisionId(),
      'type' => $log->bundle(),
      'type-name' => $log_type->label(),
      'url' => $log->toUrl('canonical', $url_options),
      'edit-url' => $log->toUrl('edit-form', $url_options),
      'created' => $log->getCreatedTime(),
      'changed' => $log->getChangedTime(),
      'author' => $log->get('uid')->entity->label(),
      'timestamp' => $log->get('timestamp')->value,
    ];
    $this->assertTokens('log', ['log' => $log], $tokens);
  }

}
