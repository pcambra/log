<?php

/**
 * @file
 * Log pages
 */

/**
 * Log view callback.
 */
function log_view($log) {
  $log_type = log_types($log->type);
  drupal_set_title(entity_label('log_type', $log_type) . ': ' . entity_label('log', $log));
  return entity_view('log', array(entity_id('log', $log) => $log), 'full');
}
