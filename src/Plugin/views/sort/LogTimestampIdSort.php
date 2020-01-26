<?php

namespace Drupal\log\Plugin\views\sort;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\sort\Date;

/**
 * Sort handler for logs based on timestamp and id.
 *
 * @ViewsSort("log_timestamp_id")
 */
class LogTimestampIdSort extends Date {

  /**
   * {@inheritdoc}
   */
  public function canExpose() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['timestamp_order'] = ['default' => 'DESC'];
    $options['id_order'] = ['default' => 'ASC'];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $options = $this->sortOptions();
    $form['timestamp_order'] = [
      '#title' => $this->t('Timestamp order'),
      '#type' => 'radios',
      '#options' => $options,
      '#default_value' => $this->options['timestamp_order'],
    ];
    $form['id_order'] = [
      '#title' => $this->t('ID order'),
      '#type' => 'radios',
      '#options' => $options,
      '#default_value' => $this->options['id_order'],
    ];
    $form['order']['#access'] = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    return $this->t('Timestamp: @timestamp, Id: @id', ['@timestamp' => strtolower($this->options['timestamp_order']), '@id' => strtolower($this->options['id_order'])]);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    switch ($this->options['granularity']) {
      case 'second':
      default:
        $this->query->addOrderBy($this->tableAlias, 'timestamp', $this->options['timestamp_order']);
        $this->query->addOrderBy($this->tableAlias, 'id', $this->options['id_order']);
        return;

      case 'minute':
        $formula = $this->getDateFormat('YmdHi');
        break;

      case 'hour':
        $formula = $this->getDateFormat('YmdH');
        break;

      case 'day':
        $formula = $this->getDateFormat('Ymd');
        break;

      case 'month':
        $formula = $this->getDateFormat('Ym');
        break;

      case 'year':
        $formula = $this->getDateFormat('Y');
        break;
    }

    $this->query->addOrderBy(NULL, $formula, $this->options['timestamp_order'], $this->tableAlias . '_timestamp_' . $this->options['granularity']);
    $this->query->addOrderBy($this->tableAlias, 'id', $this->options['id_order']);
  }

  /**
   * {@inheritdoc}
   */
  public function getDateField() {
    return $this->query->getDateField("$this->tableAlias.timestamp");
  }

}
