<?php

namespace Drupal\myroute_metatag\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Myroute metatag settings list form.
 */
class MyrouteMetatagSettingsListForm extends ConfigFormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'myroute_metatag_settings_list_form';
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['myroute_metatag.settings'];
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('myroute_metatag.settings');
    $form['view_cols'] = [
      '#type' => 'checkboxes',
      '#title' => 'Показать колонки',
      '#default_value' => !empty($config->get('list.view_cols')) ? $config->get('list.view_cols') : [],
      '#options' => [
        'route_name' => $this->t('Route Name'),
        'conditions' => 'Условия',
        'title_h1' => 'H1',
        'head_title' => 'Head title',
        'description' => 'Description',
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
    $config =  $this->config('myroute_metatag.settings');
    $config->set('list.view_cols', $view_cols);
    $config->save();
    parent::submitForm($form, $form_state);
  }


}
