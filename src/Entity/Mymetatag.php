<?php

namespace Drupal\mymetatag\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\mymetatag\MymetatagInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Mymetatag entity.
 *
 * @ingroup mymetatag
 *
 * @ContentEntityType(
 *   id = "mymetatag",
 *   label = @Translation("Mymetatag entity"),
 *   handlers = {
 *     "storage" = "Drupal\mymetatag\MymetatagStorage",
 *     "storage_schema" = "Drupal\mymetatag\MymetatagStorageSchema",
 *     "list_builder" = "Drupal\mymetatag\MymetatagListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\mymetatag\MymetatagForm",
 *       "edit" = "Drupal\mymetatag\MymetatagForm",
 *       "delete" = "Drupal\mymetatag\Form\MymetatagDeleteForm",
 *     },
 *     "access" = "Drupal\mymetatag\MymetatagAccessControlHandler",
 *   },
 *   fieldable = FALSE,
 *   translatable = TRUE,
 *   base_table = "mymetatag",
 *   data_table = "mymetatag_field_data",
 *   admin_permission = "administer mymetatag entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "source_path",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/seo/mymetatag/{mymetatag}/edit",
 *     "edit-form" = "/admin/seo/mymetatag/{mymetatag}/edit",
 *     "delete-form" = "/admin/seo/mymetatag/{mymetatag}/delete",
 *     "collection" = "/mymetatag/list"
 *   },
 *   field_ui_base_route = "entity.mymetatag.collection",
 * )
 *
 * The 'links':
 * entity.<entity-name>.<link-name>
 * Example: 'entity.mymetatag.canonical'
 *
 *  *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 */
class Mymetatag extends ContentEntityBase implements MymetatagInterface {

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'uid' => \Drupal::currentUser()->id(),
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getSourceEntityType() {
    return $this->get('source_entity_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourceEntityType($source_entity_type) {
    $this->set('source_entity_type', $source_entity_type);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceBundle() {
    return $this->get('source_bundle')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function setSourceBundle($source_bundle) {
    $this->set('source_bundle', $source_bundle);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getSourceEntityId() {
    return $this->get('source_entity_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourceEntityId($source_entity_id) {
    $this->set('source_entity_id', $source_entity_id);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getSourcePath() {
    return $this->get('source_path')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourcePath($source_path) {
    $this->set('source_path', $source_path);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getHeadTitle() {
    return $this->get('head_title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setHeadTitle($head_title) {
    $this->set('head_title', $head_title);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->set('description', $description);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getTitleH1() {
    return $this->get('title_h1')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitleH1($title_h1) {
    $this->set('title_h1', $title_h1);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getSeoTextTitle() {
    return $this->get('seo_text_title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSeoTextTitle($seo_text_title) {
    $this->set('seo_text_title', $seo_text_title);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getSeoText() {
    return $this->get('seo_text')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSeoText($seo_text) {
    $this->set('seo_text', $seo_text);
    return $this;
  }



  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
    $changed = $this->getUntranslated()->getChangedTime();

    /* @var \Drupal\Core\Language\Language $language */
    foreach ($this->getTranslationLanguages(FALSE) as $language) {
      $translation_changed = $this->getTranslation($language->getId())
        ->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['source_path'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Path'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['source_entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['source_bundle'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Bundle'))
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['source_entity_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Entity id'))
      ->setSetting('unsigned', TRUE);

    $fields['title_h1'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title h1'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['head_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Head title'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);


    $fields['description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);


    $fields['seo_text_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Seo text title'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);


    $fields['seo_text'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Seo text'))
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);


    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User Name'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'));


    return $fields;

  }
}