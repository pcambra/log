<?php

namespace Drupal\log;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines a class to build a listing of Log entities.
 *
 * @ingroup log
 */
class LogListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Log ID');
    $header['label'] = $this->t('Label');
    $header['type'] = $this->t('Type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\log\Entity\LogInterface */
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink($entity->label(), 'canonical');
    // @todo Show type name.
    $row['type'] = $entity->bundle();
    return $row + parent::buildRow($entity);
  }

}
