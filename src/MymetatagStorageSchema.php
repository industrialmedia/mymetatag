<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\Core\Field\FieldStorageDefinitionInterface;


class MymetatagStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {
    $schema = parent::getEntitySchema($entity_type, $reset);
    $schema['mymetatag_field_data']['indexes'] += [
      'mymetatag__source_path__langcode' => ['source_path', 'langcode'],
    ];
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  protected function getSharedTableFieldSchema(FieldStorageDefinitionInterface $storage_definition, $table_name, array $column_mapping) {
    $schema = parent::getSharedTableFieldSchema($storage_definition, $table_name, $column_mapping);
    $field_name = $storage_definition->getName();
    if ($table_name == 'mymetatag_field_data') {
      switch ($field_name) {
        case 'source_path':
        case 'source_entity_type':
        case 'source_bundle':
        case 'source_entity_id':
          $schema['fields'][$field_name]['not null'] = TRUE;
          break;
      }
      switch ($field_name) {
        case 'changed':
        case 'created':
        case 'source_path':
        case 'source_entity_type':
        case 'source_bundle':
        case 'source_entity_id':
          $this->addSharedTableFieldIndex($storage_definition, $schema, TRUE);
          break;
      }
    }
    return $schema;
  }

}
