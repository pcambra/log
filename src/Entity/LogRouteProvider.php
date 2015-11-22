<?php

/**
 * @file
 * Contains \Drupal\log\Entity\LogRouteProvider.
 */

namespace Drupal\log\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides routes for logs.
 */
class LogRouteProvider implements EntityRouteProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getRoutes( EntityTypeInterface $entity_type) {
    $route_collection = new RouteCollection();
    $route = (new Route('/log/{log}'))
      ->addDefaults([
        '_controller' => '\Drupal\log\Controller\LogViewController::view',
        '_title_callback' => '\Drupal\log\Controller\LogViewController::title',
      ])
      ->setRequirement('log', '\d+')
      ->setRequirement('_entity_access', 'log.view');
    $route_collection->add('entity.log.canonical', $route);

    $route = (new Route('/log/{log}/delete'))
      ->addDefaults([
        '_entity_form' => 'log.delete',
        '_title' => 'Delete',
      ])
      ->setRequirement('log', '\d+')
      ->setRequirement('_entity_access', 'log.delete')
      ->setOption('_log_operation_route', TRUE);
    $route_collection->add('entity.log.delete_form', $route);

    $route = (new Route('/log/{log}/edit'))
      ->setDefault('_entity_form', 'log.edit')
      ->setRequirement('_entity_access', 'log.update')
      ->setRequirement('log', '\d+')
      ->setOption('_log_operation_route', TRUE);
    $route_collection->add('entity.log.edit_form', $route);

    return $route_collection;
  }

}
