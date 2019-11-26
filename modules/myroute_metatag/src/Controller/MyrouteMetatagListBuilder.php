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
    $header['label'] = 'Шаблоны';
    $header['conditions'] = t('Conditions');
    $header['weight'] = t('Weight');
    $header += parent::buildHeader();
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\myroute_metatag\Entity\MyrouteMetatag $entity */
    $row['label'] = $entity->label();
    // conditions
    $row['conditions'] = [];
    if ($conditions = $entity->getConditions()) {
      $row['conditions'] = [
        '#type' => 'table',
        '#header' => [],
        '#empty' => '',
        '#attached' => [
          'library' => ['myroute_metatag/admin'],
        ]
      ];
      foreach ($conditions as $condition_id => $condition) {
        /* @var \Drupal\Core\Condition\ConditionPluginBase $condition */
        $row2 = [];
        $row2['label']['#markup'] = $condition->getPluginDefinition()['label'];
        $row2['description']['#markup'] = $condition->summary();
        $row['conditions'][$condition_id] = $row2;
      }
    }


    $row += parent::buildRow($entity);
    return $row;
  }


  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['help'] = [
      '#markup' => '<p>Первый шаблон из списка для которого все условия будут выполнены - будет использован.</p>',
      '#weight' => -10,
    ];
    return $build;
  }


}
