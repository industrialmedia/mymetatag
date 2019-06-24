<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Mymetatag entity.
 * @ingroup mymetatag
 */
interface MymetatagInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {



  /**
   * Gets the mymetatag creation timestamp.
   *
   * @return int
   *   Creation timestamp of the mymetatag.
   */
  public function getCreatedTime();



  /**
   * Gets the source_entity_type.
   *
   * @return string
   *   source_entity_type of the mymetatag.
   */
  public function getSourceEntityType();


  /**
   * Sets the mymetatag source_entity_type.
   *
   * @param string $source_entity_type
   *   The mymetatag source_entity_type.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSourceEntityType($source_entity_type);


  /**
   * Gets the source_bundle.
   *
   * @return string
   *   source_bundle of the mymetatag.
   */
  public function getSourceBundle();


  /**
   * Sets the mymetatag source_bundle.
   *
   * @param string $source_bundle
   *   The mymetatag source_bundle.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSourceBundle($source_bundle);

  /**
   * Gets the source_entity_id.
   *
   * @return int
   *   source_entity_id of the mymetatag.
   */
  public function getSourceEntityId();


  /**
   * Sets the mymetatag source_entity_id.
   *
   * @param int $source_entity_id
   *   The mymetatag source_entity_id.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSourceEntityId($source_entity_id);


  /**
   * Gets the phone.
   *
   * @return string
   *   source_path of the mymetatag.
   */
  public function getSourcePath();


  /**
   * Sets the mymetatag source_path.
   *
   * @param string $source_path
   *   The mymetatag source_path.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSourcePath($source_path);

  /**
   * Gets the head_title.
   *
   * @return string
   *   head_title of the mymetatag.
   */
  public function getHeadTitle();


  /**
   * Sets the mymetatag head_title.
   *
   * @param string $head_title
   *   The mymetatag head_title.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setHeadTitle($head_title);


  /**
   * Gets the description.
   *
   * @return string
   *   description of the mymetatag.
   */
  public function getDescription();

  /**
   * Sets the mymetatag description.
   *
   * @param string $description
   *   The mymetatag description.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setDescription($description);

  /**
   * Gets the title_h1.
   *
   * @return string
   *   title_h1 of the mymetatag.
   */
  public function getTitleH1();


  /**
   * Sets the mymetatag title_h1.
   *
   * @param string $title_h1
   *   The mymetatag title_h1.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setTitleH1($title_h1);



  /**
   * Gets the seo_text_title.
   *
   * @return string
   *   seo_text_title of the mymetatag.
   */
  public function getSeoTextTitle();


  /**
   * Sets the mymetatag seo_text_title.
   *
   * @param string $seo_text_title
   *   The mymetatag seo_text_title.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSeoTextTitle($seo_text_title);




  /**
   * Gets the seo_text.
   *
   * @return string
   *   seo_text of the mymetatag.
   */
  public function getSeoText();


  /**
   * Sets the mymetatag seo_text.
   *
   * @param string $seo_text
   *   The mymetatag seo_text.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setSeoText($seo_text);

  

  /**
   * Gets the noindex.
   *
   * @return int
   *   noindex of the mymetatag.
   */
  public function getNoindex();


  /**
   * Sets the mymetatag noindex.
   *
   * @param int $noindex
   *   The mymetatag noindex.
   *
   * @return \Drupal\mymetatag\MymetatagInterface
   *   The called mymetatag entity.
   */
  public function setNoindex($noindex);



  
}


