<?php

/**
 * @param \Drupal\log\LogInterface $log
 * @param $op
 * @param \Drupal\Core\Session\AccountInterface $account
 */
function hook_log_access(\Drupal\log\LogInterface $log, $op, \Drupal\Core\Session\AccountInterface $account) {
  // Example.
}

/**
 * @param \Drupal\Core\Session\AccountInterface $account
 * @param array $context
 * @param null $entity_bundle
 */
function hook_log_create_access(\Drupal\Core\Session\AccountInterface $account, $context = array(), $entity_bundle = NULL) {
  // Example.
}
