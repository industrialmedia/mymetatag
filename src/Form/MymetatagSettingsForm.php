<?php

namespace Drupal\mymetatag\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class MymetatagSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {


  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;


  /**
   * Constructs
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RouteProviderInterface $route_provider) {
    parent::__construct($config_factory);
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');
    /* @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = $container->get('router.route_provider');
    return new static(
      $config_factory,
      $route_provider
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymetatag_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mymetatag.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mymetatag.settings');
    $paths = $config->get('custom_paths.paths');
    $form['paths'] = [
      '#type' => 'textarea',
      '#title' => 'Метатеги для кастомных страниц',
      '#rows' => 15,
      '#default_value' => is_array($paths) ? implode("\n", $paths) : '',
      '#description' => 'Добавит таб "Метатеги" для этих одиночных страниц (главная, список новостей, контакты, ...).
                         Список системных путей, каждый с новой строки. <br />
                         Если надо добавить таб для шаблонного пути - добавте нужный роут, по аналогии mymetatag.admin.add_to_node, mymetatag.admin.add_to_term, ...',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->set('paths', []);
    if (!$form_state->isValueEmpty('paths')) {
      $valid = [];
      $invalid = [];
      foreach (explode("\n", trim($form_state->getValue('paths'))) as $path) {
        $path = trim($path);
        if (!empty($path)) {
          if ($this->pathIsValid($path)) {
            $valid[] = $path;
          }
          else {
            $invalid[] = $path;
          }
        }
      }
      if (empty($invalid)) {
        $form_state->set('paths', $valid);
      }
      elseif (count($invalid) == 1) {
        $form_state->setErrorByName('paths', $this->t('%path is not a valid.', ['%path' => reset($invalid)]));
      }
      else {
        $form_state->setErrorByName('path', $this->t('%paths are not valid.', ['%paths' => implode(', ', $invalid)]));
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('mymetatag.settings');
    $config
      ->set('custom_paths.paths', $form_state->get('paths'))
      ->save();
    drupal_flush_all_caches();
    parent::submitForm($form, $form_state);
  }


  private function pathIsValid($path) {
    if ($routes = $this->routeProvider->getRoutesByPattern($path)) {
      foreach ($routes->all() as $route_name => $route) {
        if ($route->getPath() == $path) {
          return TRUE;
        }
      }
    }
    return FALSE;
  }


}
