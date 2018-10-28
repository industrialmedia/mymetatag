<?php

namespace Drupal\myroute_metatag\Form;


use Drupal\myroute_metatag\Entity\MyrouteMetatag;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting an condition.
 */
class ConditionDeleteForm extends ConfirmFormBase {


  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The myroute_metatag entity this selection condition belongs to.
   *
   * @var \Drupal\myroute_metatag\Entity\MyrouteMetatag
   */
  protected $myroute_metatag;

  /**
   * The condition used by this form.
   *
   * @var \Drupal\Core\Condition\ConditionInterface
   */
  protected $condition;


  /**
   * Constructs
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /* @var \Drupal\Core\Messenger\MessengerInterface $messenger */
    $messenger = $container->get('messenger');
    return new static(
      $messenger
    );
  }



  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myroute_metatag_condition_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the condition %name?', ['%name' => $this->condition->getPluginDefinition()['label']]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->myroute_metatag->urlInfo('edit-form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, MyrouteMetatag $myroute_metatag = NULL, $condition_id = NULL) {
    $this->myroute_metatag = $myroute_metatag;
    $this->condition = $myroute_metatag->getCondition($condition_id);
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->myroute_metatag->removeCondition($this->condition->getConfiguration()['uuid']);
    $this->myroute_metatag->save();
    $this->messenger->addStatus($this->t('The condition %name has been removed.', ['%name' => $this->condition->getPluginDefinition()['label']]));
    $form_state->setRedirectUrl(Url::fromRoute('entity.myroute_metatag.edit_form', [
      'myroute_metatag' => $this->myroute_metatag->id(),
    ]));
  }

}
