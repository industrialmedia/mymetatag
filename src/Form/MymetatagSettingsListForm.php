<?php

namespace Drupal\mymetatag\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Mymetatag settings list form.
 */
class MymetatagSettingsListForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymetatag_settings_list_form';
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
    $form['view_cols'] = [
      '#type' => 'checkboxes',
      '#title' => 'Показать колонки',
      '#default_value' => !empty($config->get('list.view_cols')) ? $config->get('list.view_cols') : [],
      '#options' => [
        'id' => 'ID',
        'path' => 'Path',
        'title_h1' => 'H1',
        'head_title' => 'Head title',
        'description' => 'Description',
        'noindex' => 'Noindex',
        'seo_text_title' => 'Seo text title',
        'seo_text' => 'Seo text',
      ],
    ];
    return parent::buildForm($form, $form_state);
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
    $config =  $this->config('mymetatag.settings');
    $config->set('list.view_cols', $view_cols);
    $config->save();
    parent::submitForm($form, $form_state);
  }


}
