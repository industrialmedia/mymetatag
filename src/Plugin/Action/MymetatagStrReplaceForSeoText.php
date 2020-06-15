<?php

namespace Drupal\mymetatag\Plugin\Action;


use Drupal\Core\Action\ConfigurableActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Mymetatag str_replace for SEO text.
 *
 * @Action(
 *   id = "mymetatag_str_replace_for_seo_text",
 *   label = @Translation("Mymetatag str_replace for SEO text"),
 *   type = "mymetatag"
 * )
 */
class MymetatagStrReplaceForSeoText extends ConfigurableActionBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'text_search' => '',
      'text_replace' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['text_search'] = [
      '#type' => 'textfield',
      '#title' => t('Text search'),
      '#description' => t('The text search for function str_replace'),
      '#default_value' => $this->configuration['text_search'],
      '#required' => TRUE,
    ];
    $form['text_replace'] = [
      '#type' => 'textfield',
      '#title' => t('Text replace'),
      '#description' => t('The text replace for function str_replace'),
      '#default_value' => $this->configuration['text_replace'],
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['text_search'] = $form_state->getValue('text_search');
    $this->configuration['text_replace'] = $form_state->getValue('text_replace');
  }


  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\mymetatag\MymetatagInterface $entity */
    $seo_text = $entity->getSeoText();
    if ($seo_text) {
      $text_search = $this->configuration['text_search'];
      $text_replace = $this->configuration['text_replace'];
      $new_seo_text = str_replace($text_search, $text_replace, $seo_text);
      if ($seo_text != $new_seo_text) {
        $value = $entity->get('seo_text')->getValue();
        $value[0]['value'] = $new_seo_text;
        $entity->set('seo_text', $value);
        $entity->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\mymetatag\MymetatagInterface $object */
    $access = $object
      ->access('update', $account, TRUE);
    return $return_as_object ? $access : $access->isAllowed();
  }

}
