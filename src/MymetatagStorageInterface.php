<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\ContentEntityStorageInterface;


interface MymetatagStorageInterface extends ContentEntityStorageInterface {




  /**
   * Get not translation mymetatag by source_path
   *
   * @param string|null $source_path
   * @return \Drupal\mymetatag\MymetatagInterface
   */
  public function getMymetatagBySourcePath_notTranslation($source_path = NULL, $get = NULL);

  /**
   * Get translation mymetatag by source_path
   *
   * @param string|null $source_path
   * @param string|null $langcode
   * @return \Drupal\mymetatag\MymetatagInterface
   */
  public function getMymetatagBySourcePath($source_path = NULL, $langcode = NULL);


  /**
   * Get mymetatag custom paths
   *
   * @return array
   */
  public function getCustomPaths();


  /**
   * Get mymetatag form
   *
   * @param string $path
   * @param string $entity_type
   * @param string $bundle
   * @param integer $entity_id
   * @return array
   */
  public function getMymetatagForm($path, $entity_type, $bundle, $entity_id);


}
