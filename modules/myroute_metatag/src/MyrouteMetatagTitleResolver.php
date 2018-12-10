<?php

namespace Drupal\myroute_metatag;


use Drupal\Core\Controller\TitleResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Drupal\Core\Routing\RouteMatch;
use Drupal\myroute_metatag\Entity\MyrouteMetatag;
use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Drupal\Core\Utility\Token;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

class MyrouteMetatagTitleResolver extends TitleResolver  {


  /**
   * The myroute metatag evaluator.
   *
   * @var \Drupal\myroute_metatag\MyrouteMetatagEvaluator
   */
  protected $myrouteMetatagEvaluator;


  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;


  /**
   * The myroute metatag helper service.
   *
   * @var \Drupal\myroute_metatag\MyrouteMetatagHelper
   */
  protected $myrouteMetatagHelper;



  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * The mymetatag storage.
   *
   * @var \Drupal\mymetatag\MymetatagStorageInterface
   */
  protected $mymetatagStorage;





  /**
   * Constructs
   *
   * @param \Drupal\Core\Controller\ControllerResolverInterface $controller_resolver
   *   The controller resolver.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The translation manager.
   * @param \Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface $argument_resolver
   *   The argument resolver.
   * @param \Drupal\myroute_metatag\MyrouteMetatagEvaluator $myroute_metatag_evaluator
   *   The myroute metatag evaluator.
   * @param \Drupal\Core\Utility\Token $token
   *   The token utility.
   * @param \Drupal\myroute_metatag\MyrouteMetatagHelper $myroute_metatag_helper
   *   The myroute metatag helper service.
   */
  public function __construct(ControllerResolverInterface $controller_resolver, TranslationInterface $string_translation, ArgumentResolverInterface $argument_resolver, MyrouteMetatagEvaluator $myroute_metatag_evaluator, Token $token, MyrouteMetatagHelper $myroute_metatag_helper, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($controller_resolver, $string_translation, $argument_resolver);
    $this->myrouteMetatagEvaluator = $myroute_metatag_evaluator;
    $this->token = $token;
    $this->myrouteMetatagHelper = $myroute_metatag_helper;

    $this->entityTypeManager = $entity_type_manager;
    $this->mymetatagStorage = $this->entityTypeManager->getStorage('mymetatag');

  }




  /**
   * {@inheritdoc}
   */
  public function getTitle(Request $request, Route $route) {
    $route_title = parent::getTitle($request, $route);


    // Одиночный метатег
    $url = Url::createFromRequest($request);

    $source_path = '/' . $url->getInternalPath();


    if ($mymetatag = $this->mymetatagStorage->getMymetatagBySourcePath($source_path)) {
      if (!empty($mymetatag->getTitleH1())) {
        $route_title = $mymetatag->getTitleH1();
      }
    }
    else { // По шаблону

      $route_match = RouteMatch::createFromRequest($request);
      $route_name = $route_match->getRouteName();
      if ($route_name && $myroute_metatag_id = $this->myrouteMetatagEvaluator->evaluateByRouteName($route_name)) {
        $myroute_metatag = MyrouteMetatag::load($myroute_metatag_id);
        $items = $myroute_metatag->getItems();
        if (!empty($items['title_h1'])) {
          $token_types = $this->myrouteMetatagHelper->getTokenTypesByRouteName($route_name);
          $data = [];
          foreach ($token_types as $token_type => $parameter_name) {
            $parameter = $route_match->getParameter($parameter_name);
            $data[$token_type] = $parameter;
          }
          $new_title = $this->token->replace($items['title_h1'], $data, array('clear' => TRUE));
          if (!empty($new_title)) {
            $route_title = $new_title;
          }
        }
      }

    }
  
    return $route_title;
  }

}


