<?php

namespace Drupal\myroute_metatag\Controller;


use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\DraggableListBuilder;

/**
 * Provides a listing of MyrouteMetatag entities.
 */
class MyrouteMetatagListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myroute_metatag_list_builder';
  }


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('MyrouteMetatag');
    $header['weight'] = t('Weight');
    $header += parent::buildHeader();
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row += parent::buildRow($entity);
    return $row;
  }

}
