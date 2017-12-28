<?php

/**
 * @file
 * Log Views hooks.
 */

/**
 * Implements hook_views_data_alter().
 */
function log_views_data_alter(&$data) {

  // Override the default log timestamp field and sort handlers, to add an
  // additional sort by log ID.
  $data['log']['timestamp']['field']['handler'] = 'log_handler_field_timestamp';
  $data['log']['timestamp']['sort']['handler'] = 'log_handler_sort_timestamp';
}
