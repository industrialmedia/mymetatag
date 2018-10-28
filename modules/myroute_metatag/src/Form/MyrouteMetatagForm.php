<?php

namespace Drupal\myroute_metatag\Form;


use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

use Drupal\myroute_metatag\Entity\MyrouteMetatag;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Url;
use Drupal\myroute_metatag\MyrouteMetatagHelper;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Entity form for MyrouteMetatag entity.
 */
class MyrouteMetatagForm extends EntityForm implements ContainerInjectionInterface {


  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The myroute breadcrumb helper service.
   *
   * @var \Drupal\myroute_metatag\MyrouteMetatagHelper
   */
  protected $myrouteMetatagHelper;


  /**
   * Constructs
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\myroute_metatag\MyrouteMetatagHelper $myroute_metatag_helper
   *   The myroute breadcrumb helper service.
   */
  public function __construct(MessengerInterface $messenger, MyrouteMetatagHelper $myroute_metatag_helper) {
    $this->messenger = $messenger;
    $this->myrouteMetatagHelper = $myroute_metatag_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var MessengerInterface $messenger */
    $messenger = $container->get('messenger');
    /* @var MyrouteMetatagHelper $myroute_metatag_helper */
    $myroute_metatag_helper = $container->get('myroute_metatag.helper');
    return new static(
      $messenger,
      $myroute_metatag_helper
    );
  }


  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\myroute_metatag\Entity\MyrouteMetatag $myroute_metatag */
    $myroute_metatag = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $myroute_metatag->label(),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $myroute_metatag->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\myroute_metatag\Entity\MyrouteMetatag::load',
      ),
      '#disabled' => !$myroute_metatag->isNew(),
    );
    $form['route_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Route Name'),
      '#maxlength' => 255,
      '#default_value' => $myroute_metatag->getRouteName(),
      '#required' => TRUE,
      '#autocomplete_route_name' => 'myroute_metatag.router_autocomplete',
    ];
    if (!$myroute_metatag->isNew()) {
      $form['items_section'] = $this->createItemsSet($form, $form_state, $myroute_metatag);
      $form['conditions_section'] = $this->createConditionsSet($form, $myroute_metatag);
      $form['logic'] = [
        '#type' => 'radios',
        '#options' => [
          'and' => $this->t('All conditions must pass'),
          'or' => $this->t('Only one condition must pass'),
        ],
        '#default_value' => $myroute_metatag->getLogic(),
      ];
    }
    return $form;
  }


  protected function createItemsSet(array $form, FormStateInterface $form_state, MyrouteMetatag $myroute_metatag) {
    //
    $form_state->getValues();
    //
    $items = $myroute_metatag->getItems();
    $form['items_section'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Items'),
      '#open' => TRUE,
      '#prefix' => '<div id="items-section-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['items_section']['items'] = [
      '#tree' => TRUE,
    ];
    $form['items_section']['items']['title_h1'] = [
      '#type' => 'textfield',
      '#title' => 'H1',
      '#default_value' => !empty($items['title_h1']) ? $items['title_h1'] : '',
      '#size' => 160,
      '#maxlength' => 255,
    ];
    $form['items_section']['items']['head_title'] = [
      '#type' => 'textfield',
      '#title' => 'Head title',
      '#default_value' => !empty($items['head_title']) ? $items['head_title'] : '',
      '#size' => 160,
      '#maxlength' => 255,
    ];
    $form['items_section']['items']['description'] = [
      '#type' => 'textarea',
      '#title' => 'Description',
      '#default_value' => !empty($items['description']) ? $items['description'] : '',
    ];
    $form['items_section']['token_tree'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => array_keys($this->myrouteMetatagHelper->getTokenTypesByRouteName($myroute_metatag->getRouteName())),
      '#show_restricted' => TRUE,
      '#show_nested' => FALSE,
    );
    return $form['items_section'];
  }


  protected function createConditionsSet(array $form, MyrouteMetatag $myroute_metatag) {
    $attributes = [
      'class' => ['use-ajax'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode([
        'width' => 'auto',
      ]),
    ];
    $add_button_attributes = NestedArray::mergeDeep($attributes, [
      'class' => [
        'button',
        'button--small',
        'button-action',
        'form-item',
      ],
    ]);
    $form['conditions_section'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Conditions'),
      '#open' => TRUE,
    ];
    $form['conditions_section']['add_condition'] = [
      '#type' => 'link',
      '#title' => $this->t('Add new condition'),
      '#url' => Url::fromRoute('myroute_metatag.condition_select', [
        'myroute_metatag' => $myroute_metatag->id(),
      ]),
      '#attributes' => $add_button_attributes,
      '#attached' => [
        'library' => [
          'core/drupal.ajax',
        ],
      ],
    ];
    if ($conditions = $myroute_metatag->getConditions()) {
      $form['conditions_section']['conditions'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Label'),
          $this->t('Description'),
          $this->t('Operations'),
        ],
        '#empty' => $this->t('There are no conditions.'),
      ];
      foreach ($conditions as $condition_id => $condition) {
        $row = [];
        $row['label']['#markup'] = $condition->getPluginDefinition()['label'];
        $row['description']['#markup'] = $condition->summary();
        $operations = [];
        $operations['edit'] = [
          'title' => $this->t('Edit'),
          'url' => Url::fromRoute('myroute_metatag.condition_edit', [
            'myroute_metatag' => $myroute_metatag->id(),
            'condition_id' => $condition_id,
          ]),
          'attributes' => $attributes,
        ];
        $operations['delete'] = [
          'title' => $this->t('Delete'),
          'url' => Url::fromRoute('myroute_metatag.condition_delete', [
            'myroute_metatag' => $myroute_metatag->id(),
            'condition_id' => $condition_id,
          ]),
          'attributes' => $attributes,
        ];
        $row['operations'] = [
          '#type' => 'operations',
          '#links' => $operations,
        ];
        $form['conditions_section']['conditions'][$condition_id] = $row;
      }
    }
    return $form['conditions_section'];
  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $myroute_metatag = $this->entity;
    $status = $myroute_metatag->save();
    if ($status) {
      $this->messenger->addStatus($this->t('Saved the %label MyrouteMetatag.', array(
        '%label' => $myroute_metatag->label(),
      )));
    }
    else {
      $this->messenger->addStatus($this->t('The %label MyrouteMetatag was not saved.', array(
        '%label' => $myroute_metatag->label(),
      )));
    }
    $form_state->setRedirectUrl($myroute_metatag->toUrl('collection'));
  }


}
