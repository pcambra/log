<?php

/**
 * @file
 * Contains the log timestamp sort handler.
 */

/**
 * Sort handler for log timestamps.
 */
class log_handler_sort_timestamp extends views_handler_sort_date {

  /**
   * Called to add the sort to a query.
   */
  function query() {

    // Call the parent query method.
    parent::query();

    // Always order by log ID in addition to timestamp.
    $this->query->add_orderby($this->table_alias, 'id', $this->options['order']);
  }
}

