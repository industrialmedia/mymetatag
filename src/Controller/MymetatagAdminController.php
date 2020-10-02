<?php

namespace Drupal\mymetatag\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;


class MymetatagAdminController extends ControllerBase {


  /**
   * The mymetatag storage.
   *
   * @var \Drupal\mymetatag\MymetatagStorageInterface
   */
  protected $mymetatagStorage;


  /**
   * Constructs
   *
   */
  public function __construct() {
    $this->mymetatagStorage = $this->entityTypeManager()->getStorage('mymetatag');
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
    $mymetatag_form = $this->mymetatagStorage->getMymetatagForm($path, $entity_type, $bundle, $entity_id);
    return $mymetatag_form;
  }

  private function addToEntity(EntityInterface $entity) {
    $entity_type = $entity->getEntityTypeId();
    $entity_id = $entity->id();
    $bundle = $entity->bundle();
    $path = $entity->toUrl()->getInternalPath();
    $path = '/' . $path;
    $mymetatag_form = $this->mymetatagStorage->getMymetatagForm($path, $entity_type, $bundle, $entity_id);
    return $mymetatag_form;
  }

}
