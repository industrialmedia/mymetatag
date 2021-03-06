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

    $form['is_ignore_GET'] = [
      '#type' => 'checkbox',
      '#title' => 'Игнорировать страницы в которых не пустой GET',
      '#description' => 'Если в урле страницы есть GET параметры, то метатеги на этот урл не будут добавлены',
      '#default_value' => $config->get('is_ignore_GET'),
    ];
    $form['is_ignore_GET_page'] = [
      '#type' => 'checkbox',
      '#title' => "Игнорировать страницы в которых не пустой GET['page']",
      '#description' => 'Если в урле страницы есть GET параметр page, то метатеги на этот урл не будут добавлены
          (например если для пагинации сделано по шаблону)',
      '#default_value' => $config->get('is_ignore_GET_page'),
    ];

    $form['suffix_page_number__text'] = [
      '#type' => 'textfield',
      '#title' => 'Суфикс пагинации',
      '#default_value' => $config->get('suffix_page_number__text'),
      '#description' => "Добавляется в конец метатегов Title и Description для страниц с паганациец. <br />
       Если не заполнено будет использовано <em>(страница [n])</em>, <br />[n] = GET['page'] + 1 - номер страницы",
    ];
    $form['suffix_page_number__is_hidden'] = [
      '#type' => 'checkbox',
      '#title' => "Не добавлять суфикс пагинации",
      '#description' => 'Пример у нас есть метатеги по шаблону, где уже добавлена пагинация, и не нужно дублировать',
      '#default_value' => $config->get('suffix_page_number__is_hidden'),
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
          } else {
            $invalid[] = $path;
          }
        }
      }
      if (empty($invalid)) {
        $form_state->set('paths', $valid);
      } elseif (count($invalid) == 1) {
        $form_state->setErrorByName('paths', $this->t('%path is not a valid.', ['%path' => reset($invalid)]));
      } else {
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
      ->set('is_ignore_GET', $form_state->getValue('is_ignore_GET'))
      ->set('is_ignore_GET_page', $form_state->getValue('is_ignore_GET_page'))
      ->set('suffix_page_number__text', $form_state->getValue('suffix_page_number__text'))
      ->set('suffix_page_number__is_hidden', $form_state->getValue('suffix_page_number__is_hidden'))
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
