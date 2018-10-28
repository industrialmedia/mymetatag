<?php

namespace Drupal\mymetatag\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mymetatag\Entity\Mymetatag;
use Drupal\mymetatag\MymetatagStorage;

class MymetatagAdminController extends ControllerBase {

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The mymetatag storage.
   *
   * @var \Drupal\mymetatag\MymetatagStorageInterface
   */
  protected $mymetatagStorage;


  /**
   * Constructs
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\mymetatag\MymetatagStorage $mymetatag_storage
   *   The database mymetatag storage.
   */
  public function __construct(Connection $database, MymetatagStorage $mymetatag_storage) {
    $this->database = $database;
    $this->mymetatagStorage = $mymetatag_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Database\Connection $database */
    $database = $container->get('database');
    return new static(
      $database,
      $container->get('entity.manager')->getStorage('mymetatag')
    );
  }


  public function addToNode(EntityInterface $node) {
    return $this->addToEntity($node);
  }

  public function addToTerm(EntityInterface $taxonomy_term) {
    return $this->addToEntity($taxonomy_term);
  }

  public function addToCommerceProduct(EntityInterface $commerce_product) {
    return $this->addToEntity($commerce_product);
  }

  public function addToCustomPage($path) {
    $entity_type = 'custom';
    $bundle = $path;
    $entity_id = 0;
    $mymetatag = $this->mymetatagStorage->getMymetatagBySourcePath($path);
    if (!$mymetatag) {
      $mymetatag = Mymetatag::create();
      $mymetatag->setSourcePath($path);
      $mymetatag->setSourceEntityType($entity_type);
      $mymetatag->setSourceBundle($bundle);
      $mymetatag->setSourceEntityId($entity_id);
    }
    $mymetatag_form = $this->entityFormBuilder()->getForm($mymetatag, 'edit');
    return $mymetatag_form;
  }


  private function addToEntity(EntityInterface $entity) {
    $entity_type = $entity->getEntityTypeId();
    $entity_id = $entity->id();
    $bundle = $entity->bundle();
    $path = $entity->toUrl()->getInternalPath();
    $path = '/' . $path;
    $mymetatag = $this->mymetatagStorage->getMymetatagBySourcePath($path);
    if (!$mymetatag) {
      $mymetatag = Mymetatag::create();
      $mymetatag->setSourcePath($path);
      $mymetatag->setSourceEntityType($entity_type);
      $mymetatag->setSourceBundle($bundle);
      $mymetatag->setSourceEntityId($entity_id);
    }
    $mymetatag_form = $this->entityFormBuilder()->getForm($mymetatag, 'edit');
    return $mymetatag_form;
  }
  
  
}