<?php

namespace Drupal\myroute_metatag\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;


/**
 * Myroute metatag settings list form.
 */
class MyrouteMetatagSettingsListForm extends FormBase implements ContainerInjectionInterface {


  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new MyrouteMetatagSettingsListForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');
    return new static(
      $config_factory
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myroute_metatag_settings_list_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('myroute_metatag.settings');
    $form['view_cols'] = [
      '#type' => 'checkboxes',
      '#title' => 'Показать колонки',
      '#default_value' => !empty($config->get('list.view_cols')) ? $config->get('list.view_cols') : [],
      '#options' => [
        'route_name' => $this->t('Route Name'),
        'conditions' => 'Условия',
        'title_h1' => 'H1',
        'head_title' => 'Head title',
        'description' => 'Description',
      ],
    ];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $view_cols = array_filter($values['view_cols']);
    if ($view_cols) {
      $view_cols = array_values($view_cols);
    }
    $config = $this->configFactory->getEditable('myroute_metatag.settings');
    $config->set('list.view_cols', $view_cols);
    $config->save();
  }


}
