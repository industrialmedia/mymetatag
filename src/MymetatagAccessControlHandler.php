<?php

namespace Drupal\mymetatag;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;


class MymetatagAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view mymetatag entity');
      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit mymetatag entity');
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete mymetatag entity');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add mymetatag entity');
  }

}

?>