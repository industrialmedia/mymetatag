<?php

namespace Drupal\myroute_metatag\Controller;

use Drupal\myroute_metatag\Entity\MyrouteMetatag;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MyrouteMetatagConditionController.
 *
 * @package Drupal\myroute_metatag\Controller
 */
class MyrouteMetatagConditionController extends ControllerBase {

  /**
   * Drupal\Core\Condition\ConditionManager definition.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;


  /**
   * Constructs
   *
   * @param \Drupal\Core\Condition\ConditionManager $condition_manager
   *   The condition manager.
   */
  public function __construct(ConditionManager $condition_manager) {
    $this->conditionManager = $condition_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Condition\ConditionManager $condition_manager */
    $condition_manager = $container->get('plugin.manager.condition');
    return new static(
      $condition_manager
    );
  }



  /**
   * Presents a list of conditions to add to the myroute_metatag entity.
   *
   * @param \Drupal\myroute_metatag\Entity\MyrouteMetatag $myroute_metatag
   *   The myroute_metatag entity.
   * @return array
   *   The condition selection page.
   */
  public function selectCondition(MyrouteMetatag $myroute_metatag) {
    $build = [
      '#theme' => 'links',
      '#links' => [],
    ];
    $available_plugins = $this->conditionManager->getDefinitions();
    foreach ($available_plugins as $condition_id => $condition) {
      $build['#links'][$condition_id] = [
        'title' => $condition['label'],
        'url' => Url::fromRoute('myroute_metatag.condition_add', [
          'myroute_metatag' => $myroute_metatag->id(),
          'condition_id' => $condition_id,
        ]),
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 'auto',
          ]),
        ],
      ];
    }
    return $build;
  }

}
