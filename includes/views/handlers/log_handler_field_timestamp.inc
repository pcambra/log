<?php

/**
 * @file
 * Contains the log timestamp field handler.
 */

/**
 * Field handler for log timestamps.
 */
class log_handler_field_timestamp extends views_handler_field_date {

  /**
   * Called to determine what to tell the click sorter.
   */
  function click_sort($order) {

    // First apply the parent click sort.
    parent::click_sort($order);

    // Always order by log ID in addition to timestamp. Use the same condition
    // in parent::click_sort().
    if (isset($this->field_alias)) {
      $this->query->add_orderby($this->table_alias, 'id', $order);
    }
  }
}

