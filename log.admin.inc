<?php

/**
 * @file
 * Log admin pages
 */

/**
 * Generates the log type editing form.
 */
function log_type_form($form, &$form_state, $log_type, $op = 'edit') {

  if ($op == 'clone') {
    $log_type->label .= ' (cloned)';
    $log_type->type = '';
  }

  $form['label'] = array(
    '#title' => t('Label'),
    '#type' => 'textfield',
    '#default_value' => $log_type->label,
    '#description' => t('The human-readable name of this log type.'),
    '#required' => TRUE,
    '#size' => 30,
  );

  // Machine-readable type name.
  $form['type'] = array(
    '#type' => 'machine_name',
    '#default_value' => !empty($log_type->type) ? $log_type->type : '',
    '#maxlength' => 32,
    '#disabled' => $log_type->isLocked() && $op != 'clone',
    '#machine_name' => array(
      'exists' => 'log_types',
      'source' => array('label'),
    ),
    '#description' => t('A unique machine-readable name for this log type. It must only contain lowercase letters, numbers, and underscores.'),
  );

  // Name pattern.
  $form['name_pattern'] = array(
    '#type' => 'textfield',
    '#title' => t('Naming pattern'),
    '#desription' => t('When a log name is auto-generated, this is the naming pattern that will be used. Available tokens are below.'),
    '#default_value' => !empty($log_type->name_pattern) ? $log_type->name_pattern : 'Log [log:id]',
    '#required' => TRUE,
    '#maxlength' => 256,
  );

  if (module_exists('token')) {
    $form['token_help'] = array(
      '#title' => t('Replacement patterns'),
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#description' => t('Prefer raw-text replacements for text to avoid problems with HTML entities!'),
    );
    $form['token_help']['help'] = array(
      '#theme' => 'token_tree',
      '#token_types' => array('log'),
    );
  }

  // Allow name editing.
  $form['name_edit'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow name editing'),
    '#description' => t('Check this to allow users to edit log names. Otherwise, log names will always be auto-generated.'),
    '#default_value' => !empty($log_type->name_edit) ? $log_type->name_edit : FALSE,
  );

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save log type'),
    '#weight' => 40,
  );

  if (!$log_type->isLocked() && $op != 'add' && $op != 'clone') {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete log type'),
      '#weight' => 45,
      '#limit_validation_errors' => array(),
      '#submit' => array('log_type_form_submit_delete')
    );
  }
  return $form;
}

/**
 * Submit handler for creating/editing log_type.
 */
function log_type_form_submit(&$form, &$form_state) {
  $log_type = entity_ui_form_submit_build_entity($form, $form_state);
  // Save and go back.
  log_type_save($log_type);

  // Redirect user back to list of log types.
  $form_state['redirect'] = 'admin/structure/log-types';
}

function log_type_form_submit_delete(&$form, &$form_state) {
  $form_state['redirect'] = 'admin/structure/log-types/' . $form_state['log_type']->type . '/delete';
}

/**
 * Log type delete form.
 */
function log_type_form_delete_confirm($form, &$form_state, $log_type) {
  $form['log_type'] = array(
    '#type' => 'value',
    '#value' => $log_type,
  );
  // Always provide entity id in the same form key as in the entity edit form.
  $form['log_type_id'] = array('#type' => 'value', '#value' => entity_id('log_type' ,$log_type));
  return confirm_form($form,
    t('Are you sure you want to delete log type %title?', array('%title' => entity_label('log_type', $log_type))),
    'log/' . entity_id('log_type' ,$log_type),
    t('This action cannot be undone.'),
    t('Delete'),
    t('Cancel')
  );
}

/**
 * Log type delete form submit handler.
 */
function log_type_form_delete_confirm_submit($form, &$form_state) {
  $log_type = $form_state['values']['log_type'];
  log_type_delete($log_type);

  watchdog('log_type', '@type: deleted %title.', array('@type' => $log_type->type, '%title' => $log_type->label));
  drupal_set_message(t('@type %title has been deleted.', array('@type' => $log_type->type, '%title' => $log_type->label)));

  $form_state['redirect'] = 'admin/structure/log-types';
}
