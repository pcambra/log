<?php

/**
 * @file
 * Log pages
 */

/**
 * Log view callback.
 */
function log_view($log) {
  drupal_set_title(entity_label('log', $log));
  return entity_view('log', array(entity_id('log', $log) => $log), 'full');
}

/**
 * Page to select log Type to add new log.
 */
function log_add_types_page() {
  $items = array();
  foreach (log_types() as $log_type_key => $log_type) {
    $items[] = l(entity_label('log_type', $log_type), 'log/add/' . $log_type_key);
  }
  return array(
    'list' => array(
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => t('Select a type of log to create.'),
    ),
  );
}

/**
 * Add new log page callback.
 */
function log_add($type) {
  $log_type = log_types($type);

  $log = entity_create('log', array('type' => $type));
  drupal_set_title(t('Log @name', array('@name' => entity_label('log_type', $log_type))));

  $output = drupal_get_form('log_form', $log);

  return $output;
}

/**
 * Log Form.
 */
function log_form($form, &$form_state, $log) {

  $form['log'] = array(
    '#type' => 'value',
    '#value' => $log,
  );

  // Load the log type.
  $log_type = log_type_load($log->type);

  // Only display the name field if it's editable.
  if (!empty($log_type->name_edit)) {
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#description' => t('Leave this blank to automatically generate a name.'),
      '#default_value' => $log->name,
      '#weight' => -100,
    );
  }

  $form['uid'] = array(
    '#type' => 'value',
    '#value' => $log->uid,
  );

  field_attach_form('log', $log, $form, $form_state);

  $submit = array();
  if (!empty($form['#submit'])) {
    $submit += $form['#submit'];
  }

  $form['actions'] = array(
    '#weight' => 100,
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save log'),
    '#submit' => $submit + array('log_form_submit'),
  );

  // Show Delete button if allowed.
  $log_id = entity_id('log' ,$log);
  if (!empty($log_id) && log_access('delete', $log)) {

    // Get the destination query parameter. If it is the current path, change to <front>
    // (because the current path won't exist once the log is deleted).
    $destination = drupal_get_destination();
    if ($destination['destination'] == current_path()) {
      $destination['destination'] = '<front>';
    }
    $form['actions']['delete'] = array(
      '#type' => 'markup',
      '#markup' => l(t('Delete'), 'log/' . $log_id . '/delete', array('query' => $destination)),
    );
  }

  return $form;
}

/**
 * Log validate handler.
 */
function log_form_validate($form, &$form_state) {

}

/**
 * Log submit handler.
 */
function log_form_submit($form, &$form_state) {
  $log = $form_state['values']['log'];

  entity_form_submit_build_entity('log', $log, $form, $form_state);

  log_save($log);

  $log_uri = entity_uri('log', $log);

  $form_state['redirect'] = $log_uri['path'];

  drupal_set_message(t('Log %title saved.', array('%title' => entity_label('log', $log))));
}

/**
 * Delete confirmation form.
 */
function log_delete_form($form, &$form_state, $log) {
  $form['log'] = array(
    '#type' => 'value',
    '#value' => $log,
  );
  // Always provide entity id in the same form key as in the entity edit form.
  $form['log_type_id'] = array('#type' => 'value', '#value' => entity_id('log' ,$log));
  $log_uri = entity_uri('log', $log);
  return confirm_form($form,
    t('Are you sure you want to delete log %title?', array('%title' => entity_label('log', $log))),
    $log_uri['path'],
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Delete form submit handler.
 */
function log_delete_form_submit($form, &$form_state) {
  $log = $form_state['values']['log'];
  log_delete($log);

  drupal_set_message(t('Log %title deleted.', array('%title' => entity_label('log', $log))));

  $form_state['redirect'] = '<front>';
}