<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Component\Datetime\TimeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityRepositoryInterface;


/**
 * Form controller for the mymetatag entity edit forms.
 *
 * @ingroup mymetatag
 */
class MymetatagForm extends ContentEntityForm {


  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;


  /**
   * Constructs a new OrderForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info, TimeInterface $time, RouteProviderInterface $route_provider) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository */
    $entity_repository = $container->get('entity.repository');
    /* @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info */
    $entity_type_bundle_info = $container->get('entity_type.bundle.info');
    /* @var \Drupal\Component\Datetime\TimeInterface $time */
    $time = $container->get('datetime.time');
    /* @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = $container->get('router.route_provider');
    return new static(
      $entity_repository,
      $entity_type_bundle_info,
      $time,
      $route_provider
    );
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\mymetatag\Entity\Mymetatag */
    $form = parent::buildForm($form, $form_state);

    /* @var $callback_object \Drupal\mymetatag\MymetatagForm */
    $callback_object = $form_state->getBuildInfo()['callback_object'];
    $operation = $callback_object->getOperation();
    if ($operation == 'edit') {
      $form['source_path']['#disabled'] = 'disabled';
    }
    /*
    $form['source_path'] = array(
        '#title' => $this->t('Language'),
        '#type' => 'language_select',
        '#default_value' => $entity->getUntranslated()->language()->getId(),
        '#languages' => Language::STATE_ALL,
        '#weight' => 99,
    );
    */
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public
  function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);
    /* @var $mymetatag \Drupal\mymetatag\Entity\Mymetatag */
    $mymetatag = $this->entity;
    if ($status == SAVED_UPDATED) {
      $this->messenger()
        ->addStatus($this->t('The metatag %feed has been updated.', [
          '%feed' => $mymetatag->toLink()
            ->toString()
        ]));
    }
    else {
      $this->messenger()
        ->addStatus($this->t('The metatag %feed has been added.', [
          '%feed' => $mymetatag->toLink()
            ->toString()
        ]));
    }

    // Redirect
    $url = Url::fromUserInput($mymetatag->getSourcePath());
    $form_state->setRedirectUrl($url);

    return $status;
  }
}

