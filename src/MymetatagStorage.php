<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;





class MymetatagStorage extends SqlContentEntityStorage implements MymetatagStorageInterface {


  /**
   * The current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;


  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;


  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;



  /**
   * Constructs a new CommerceContentEntityStorage object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection to be used.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend to be used.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Cache\MemoryCache\MemoryCacheInterface|null $memory_cache
   *   The memory cache backend to be used.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   *  @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(EntityTypeInterface $entity_type, Connection $database, EntityFieldManagerInterface $entity_field_manager, CacheBackendInterface $cache, LanguageManagerInterface $language_manager, MemoryCacheInterface $memory_cache = NULL, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, EntityTypeManagerInterface $entity_type_manager = NULL, CurrentPathStack $current_path, ConfigFactoryInterface $config_factory, RouteProviderInterface $route_provider, EntityRepositoryInterface $entity_repository) {
    parent::__construct($entity_type, $database, $entity_field_manager, $cache, $language_manager, $memory_cache, $entity_type_bundle_info, $entity_type_manager);
    $this->currentPath = $current_path;
    $this->configFactory = $config_factory;
    $this->routeProvider = $route_provider;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $database = $container->get('database');
    $entity_field_manager = $container->get('entity_field.manager');
    $cache = $container->get('cache.entity');
    $language_manager = $container->get('language_manager');
    $memory_cache = $container->get('entity.memory_cache');
    $entity_type_bundle_info = $container->get('entity_type.bundle.info');
    $entity_type_manager = $container->get('entity_type.manager');
    $current_path = $container->get('path.current');
    $config_factory = $container->get('config.factory');
    $route_provider = $container->get('router.route_provider');
    $entity_repository = $container->get('entity.repository');

    return new static(
      $entity_type,
      $database,
      $entity_field_manager,
      $cache,
      $language_manager,
      $memory_cache,
      $entity_type_bundle_info,
      $entity_type_manager,
      $current_path,
      $config_factory,
      $route_provider,
      $entity_repository
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getMymetatagBySourcePath($source_path = NULL, $langcode = NULL) {
    if (empty($source_path)) {
      $source_path = $this->currentPath->getPath();
    }
    $mymetatags = $this->loadByProperties([
      'source_path' => $source_path,
    ]);
    if ($mymetatag = reset($mymetatags)) {
      if (empty($langcode)) {
        $langcode = $this->languageManager->getCurrentLanguage()->getId();
      }
      $mymetatag = $this->entityRepository->getTranslationFromContext($mymetatag, $langcode);
      return $mymetatag;
    }
    return NULL;
  }


  /**
   * {@inheritdoc}
   */
  public function getCustomPaths($get_route_name = TRUE) {

    $config = $this->configFactory->get('mymetatag.settings');
    $paths = $config->get('custom_paths.paths');
    $new_paths = [];
    if ($paths) {
      foreach ($paths as $path) {
        $path_name = substr($path, 1);
        $path_name = str_replace('/', '_', $path_name);
        $new_paths[$path]['path_name'] = $path_name;
        if ($get_route_name) {
          if ($routes = $this->routeProvider->getRoutesByPattern($path)) {
            foreach ($routes->all() as $route_name => $route) {
              if ($route->getPath() == $path) {
                $new_paths[$path]['route_name'] = $route_name;
              }
            }
          }
        }
      }
    }
    return $new_paths;
  }


}
