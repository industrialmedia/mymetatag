<?php

namespace Drupal\mymetatag\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mymetatag\MymetatagStorage;


/**
 * Provides local tasks for each search page.
 */
class MymetatagLocalTask extends DeriverBase implements ContainerDeriverInterface {


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
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')->getStorage('mymetatag')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];
    $paths = $this->mymetatagStorage->getCustomPaths();
    if ($paths) {
      foreach ($paths as $path => $value) {
        if (!empty($value['route_name']) && !empty($value['path_name'])) {
          $route_name = $value['route_name'];
          $path_name = $value['path_name'];
          $this->derivatives[$route_name] = [
            'title' => t('View'),
            'route_name' => $route_name,
            'base_route' => $route_name,
          ];
          $this->derivatives['mymetatag.admin.add_to_custom_page.' . $path_name] = [
            'title' => 'Метатеги',
            'route_name' => 'mymetatag.admin.add_to_custom_page.' . $path_name,
            'base_route' => $route_name,
            'weight' => 10,
          ];
        }
      }
    }
    return $this->derivatives;
  }

}
