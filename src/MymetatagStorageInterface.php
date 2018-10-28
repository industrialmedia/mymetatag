<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\ContentEntityStorageInterface;


interface MymetatagStorageInterface extends ContentEntityStorageInterface {


  /**
   * Get mymetatag by source_path
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


}
