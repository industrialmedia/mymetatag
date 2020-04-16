<?php

namespace Drupal\myroute_metatag\Controller;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\DraggableListBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;


/**
 * Provides a listing of MyrouteMetatag entities.
 */
class MyrouteMetatagListBuilder extends DraggableListBuilder {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The view cols.
   *
   * @var array
   */
  protected $viewCols;


  /**
   * Constructs a new SlickListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ConfigFactoryInterface $config_factory) {
    parent::__construct($entity_type, $storage);
    $this->configFactory = $config_factory;
    $config = $this->configFactory->get('myroute_metatag.settings');
    $view_cols = $config->get('list.view_cols');
    $this->viewCols = $view_cols ? $view_cols : [];
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    /* @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $config_factory
    );
  }


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
    if (in_array('route_name', $this->viewCols)) {
      $header['route_name'] = $this->t('Route Name');
    }
    if (in_array('conditions', $this->viewCols)) {
      $header['conditions'] = t('Conditions');
    }
    if (in_array('title_h1', $this->viewCols)) {
      $header['title_h1'] = t('H1');
    }
    if (in_array('head_title', $this->viewCols)) {
      $header['head_title'] = t('Head title');
    }
    if (in_array('description', $this->viewCols)) {
      $header['description'] = t('Description');
    }
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
    $items = $entity->getItems();
    // route_name
    if (in_array('route_name', $this->viewCols)) {
      $row['route_name']['#markup'] = $entity->getRouteName();
    }
    // conditions
    if (in_array('conditions', $this->viewCols)) {
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
    }
    // title_h1
    if (in_array('title_h1', $this->viewCols)) {
      $row['title_h1']['#markup'] = $items['title_h1'];
    }
    // head_title
    if (in_array('head_title', $this->viewCols)) {
      $row['head_title']['#markup'] = $items['head_title'];
    }
    // description
    if (in_array('description', $this->viewCols)) {
      $row['description']['#markup'] = $items['description'];
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
    $build = [
      'form_settings' => $this->formBuilder->getForm('Drupal\myroute_metatag\Form\MyrouteMetatagSettingsListForm'),
      'list_rows' => $build,
    ];
    return $build;
  }


}
