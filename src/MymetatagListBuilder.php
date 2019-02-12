<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;


/**
 * Provides a list controller for mymetatag entity.
 * @ingroup mymetatag
 */
class MymetatagListBuilder extends EntityListBuilder {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;


  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;


  /**
   * Constructs a new ShippingMethodListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, EntityManagerInterface $entity_manager, RouteProviderInterface $route_provider) {
    parent::__construct($entity_type, $storage);
    $this->entityManager = $entity_manager;
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('entity.manager'),
      $container->get('router.route_provider')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['description'] = [
      '#markup' => 'Список метатегов',
    ];
    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('MetatagID');
    $header['path'] = $this->t('Path');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $mymetatag) {
    /* @var $mymetatag \Drupal\mymetatag\Entity\Mymetatag */
    $row['id'] = $mymetatag->id();
    $source_path = $mymetatag->getSourcePath();
    $url = Url::fromUserInput($source_path);
    $path_link = Link::fromTextAndUrl($source_path, $url);
    $row['path'] = $path_link;
    return $row + parent::buildRow($mymetatag);
  }

}
