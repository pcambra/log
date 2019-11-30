<?php

namespace Drupal\log\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Log CRUD.
 */
abstract class LogTestBase extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   *
   * Note that when a child class declares its own $modules list, that list
   * doesn't override this one, it just extends it.
   *
   * @see \Drupal\simpletest\WebTestBase::installModulesFromClassProperty()
   */
  public static $modules = [
    'user',
    'log',
    'log_test',
    'field',
    'text',
  ];

  /**
   * A test user with administrative privileges.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser($this->getAdministratorPermissions());
    $this->drupalLogin($this->adminUser);
    drupal_flush_all_caches();
  }

  /**
   * Gets the permissions for the admin user.
   *
   * @return string[]
   *   The permissions.
   */
  protected function getAdministratorPermissions() {
    return [
      'access administration pages',
      'administer logs',
      'administer log module',
      'create default log entities',
      'view any default log entities',
      'edit any default log entities',
      'delete any default log entities',
      'view default revisions',
      'revert default revisions',
      'delete default revisions',
    ];
  }

  /**
   * Creates a log entity.
   *
   * @param array $values
   *   Array of values to feed the entity.
   *
   * @return \Drupal\log\LogInterface
   *   The log entity.
   */
  protected function createLogEntity(array $values = []) {
    $storage = \Drupal::service('entity_type.manager')->getStorage('log');
    $entity = $storage->create($values + [
      'name' => $this->randomMachineName(),
      'user_id' => $this->loggedInUser->id(),
      'created' => REQUEST_TIME,
      'type' => 'default',
      'done' => TRUE,
    ]);
    return $entity;
  }

}
