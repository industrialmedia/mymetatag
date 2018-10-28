<?php

namespace Drupal\mymetatag\Routing;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Drupal\mymetatag\MymetatagStorage;

/**
 * Provides dynamic routes for search.
 */
class MymetatagRoutes implements ContainerInjectionInterface {


  /**
   * The mymetatag storage.
   *
   * @var \Drupal\mymetatag\MymetatagStorageInterface
   */
  protected $mymetatagStorage;


  /**
   * Constructs
   *
   * @param \Drupal\mymetatag\MymetatagStorage $mymetatag_storage
   *   The database mymetatag storage.
   */
  public function __construct(MymetatagStorage $mymetatag_storage) {
    $this->mymetatagStorage = $mymetatag_storage;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('mymetatag')
    );
  }

  /**
   * Returns an array of route objects.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   An array of route objects.
   */
  public function routes() {
    $routes = [];
    $paths = $this->mymetatagStorage->getCustomPaths();
    if ($paths) {
      foreach ($paths as $path => $value) {
        $path_name = $value['path_name'];
        $routes['mymetatag.admin.add_to_custom_page.' . $path_name] = new Route(
          '/admin/mymetatag/custom-page/' . $path_name,
          [
            '_controller' => 'Drupal\mymetatag\Controller\MymetatagAdminController::addToCustomPage',
            '_title' => 'Metatags',
            'path' => $path,
          ],
          [
            '_permission' => 'edit mymetatag entity',
          ]
        );
      }
    }
    return $routes;
  }

}
