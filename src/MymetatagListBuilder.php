<?php

namespace Drupal\mymetatag;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBuilderInterface;


/**
 * Provides a list controller for mymetatag entity.
 * @ingroup mymetatag
 */
class MymetatagListBuilder extends EntityListBuilder {

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
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;


  /**
   * Constructs a new SlickListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ConfigFactoryInterface $config_factory, FormBuilderInterface $form_builder) {
    parent::__construct($entity_type, $storage);
    $this->configFactory = $config_factory;
    $config = $this->configFactory->get('mymetatag.settings');
    $view_cols = $config->get('list.view_cols');
    $this->viewCols = $view_cols ? $view_cols : [];
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    /* @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');
    /* @var \Drupal\Core\Form\FormBuilderInterface $form_builder */
    $form_builder = $container->get('form_builder');
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $config_factory,
      $form_builder
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymetatag_list_builder';
  }


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    if (in_array('id', $this->viewCols)) {
      $header['id'] = $this->t('ID');
    }
    if (in_array('path', $this->viewCols)) {
      $header['path'] = $this->t('Path');
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
    if (in_array('noindex', $this->viewCols)) {
      $header['noindex'] = t('Noindex');
    }
    if (in_array('seo_text_title', $this->viewCols)) {
      $header['seo_text_title'] = $this->t('Seo text title');
    }
    if (in_array('seo_text', $this->viewCols)) {
      $header['seo_text'] = $this->t('Seo text');
    }
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $mymetatag) {
    /* @var $mymetatag \Drupal\mymetatag\Entity\Mymetatag */
    $row = [];
    // id
    if (in_array('id', $this->viewCols)) {
      $row['id'] = $mymetatag->id();
    }
    // path
    if (in_array('path', $this->viewCols)) {
      $source_path = $mymetatag->getSourcePath();
      $url = Url::fromUserInput($source_path);
      $path_link = Link::fromTextAndUrl($source_path, $url);
      $row['path'] = $path_link;
    }
    // title_h1
    if (in_array('title_h1', $this->viewCols)) {
      $row['title_h1']['#markup'] = $mymetatag->getTitleH1();
    }
    // head_title
    if (in_array('head_title', $this->viewCols)) {
      $row['head_title']['#markup'] = $mymetatag->getHeadTitle();
    }
    // description
    if (in_array('description', $this->viewCols)) {
      $row['description']['#markup'] = $mymetatag->getDescription();
    }
    // noindex
    if (in_array('noindex', $this->viewCols)) {
      $row['noindex'] = $mymetatag->getNoindexText();
    }
    // seo_text_title
    if (in_array('seo_text_title', $this->viewCols)) {
      $row['seo_text_title'] = $this->trimRow($mymetatag->getSeoTextTitle());
    }
    // seo_text
    if (in_array('seo_text', $this->viewCols)) {
      $row['seo_text'] = $this->trimRow($mymetatag->getSeoText());
    }


    return $row + parent::buildRow($mymetatag);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $link = Link::createFromRoute('метатеги по шаблонам', 'entity.myroute_metatag.collection');
    $link = $link->toString();

    $build = parent::render();
    $build['help'] = [
      '#markup' => '<p>
        Добавить мета теги для конкретной страницы можно только на странице для которой они нужны, через таб - Метатеги.<br />
        Метатеги для конкретной страницы имеют больший приоритет чем ' . $link . ',
        поэтому они будут использованы даже если есть шаблон для этих страниц.<br />
        Для страницы можно указать: <strong>h1, тайтл, дескрипшен, сео-текст, noindex</strong></p>',
      '#weight' => -10,
    ];
    $build = [
      'form_settings' => $this->formBuilder->getForm('Drupal\mymetatag\Form\MymetatagSettingsListForm'),
      'list_rows' => $build,
    ];
    return $build;


  }


  private function trimRow($str) {
    $str = strip_tags($str);
    $str = trim($str);
    $par['max_length'] = 200; // количество символов
    $par['word_boundary'] = TRUE; // обрезать только целые слова
    $par['ellipsis'] = TRUE; // добавить многоточие
    $par['html'] = TRUE; // строка может содержать html
    return FieldPluginBase::trimText($par, $str);
  }

}
