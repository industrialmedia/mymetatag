<?php

namespace Drupal\mymetatag\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\mymetatag\MymetatagStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * @Block(
 *   id = "mymetatag_seo_text",
 *   admin_label = "СЕО текст",
 * )
 */
class MymetatagSeoTextBlock extends BlockBase implements ContainerFactoryPluginInterface {

  
  /**
   * The mymetatag storage.
   *
   * @var \Drupal\mymetatag\MymetatagStorageInterface
   */
  protected $mymetatagStorage;

  /**
   * Constructs a new CartBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\mymetatag\MymetatagStorage $mymetatag_storage
   *   The database mymetatag storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MymetatagStorage $mymetatag_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->mymetatagStorage = $mymetatag_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('mymetatag')
    );
  }



  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $config = [
      'is_show_title' => TRUE,
    ];
    return $config;
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $form['is_show_title'] = [
      '#type' => 'checkbox',
      '#title' => 'Показывать "СЕО текст" заголовок',
      '#default_value' => $config['is_show_title'],
    ];
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['is_show_title'] = $form_state->getValue('is_show_title');
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $build = [];
    $build['#cache']['contexts'] = ['route'];

    $mymetatag = $this->mymetatagStorage->getMymetatagBySourcePath();
    if (empty($mymetatag)) {
      return $build;
    }

    $build['#cache']['tags'] = $mymetatag->getCacheTags();

    $seo_text = trim($mymetatag->getSeoText());
    if (empty($seo_text)) {
      return $build;
    }

    $build['#attributes']['class'][] = 'block-seo-text';
    $seo_text_title = trim($mymetatag->getSeoTextTitle());
    if ($config['is_show_title'] && $seo_text_title) {
      $build['#attributes']['class'][] = 'block-seo-text-has-title';
      $build['seo_text_title'] = [
        '#type' => 'markup',
        '#markup' => '<div class="seo-text-title"><div class="seo-text-title-in">' . $seo_text_title. '</div></div>',
      ];
    }
    $build['seo_text'] = [
      '#type' => 'markup',
      '#markup' => '<div class="seo-text"><div class="seo-text-in">' . $seo_text . '</div></div>',
    ];
    return $build;
  }




}