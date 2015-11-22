<?php

/**
 * @file
 * Contains log.page.inc..
 *
 * Page callback for Log entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Log templates.
 *
 * Default template: log.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_log(array &$variables) {
  // Fetch Log Entity Object.
  $log = $variables['elements']['#log'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}

/**
 * Prepares variables for list of available log type templates.
 *
 * Default template: log-add-list.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - content: An array of content types.
 *
 * @see log_add_page()
 */
function template_preprocess_log_add_list(&$variables) {
  $variables['types'] = array();
  if (!empty($variables['content'])) {
    foreach ($variables['content'] as $type) {
      $variables['types'][$type->id()] = array(
        'type' => $type->id(),
        'add_link' => \Drupal::l($type->label(), new Url('log.add', array('log_type' => $type->id()))),
      );
    }
  }
}