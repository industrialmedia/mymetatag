<?php

namespace Drupal\myroute_metatag;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;



class MyrouteMetatagServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('title_resolver');
    $definition->setClass('Drupal\myroute_metatag\MyrouteMetatagTitleResolver');
    $definition->addArgument(new Reference('myroute_metatag.myroute_metatag_evaluator'));
    $definition->addArgument(new Reference('token'));
    $definition->addArgument(new Reference('myroute_metatag.helper'));
    $definition->addArgument(new Reference('entity_type.manager'));
  }

}