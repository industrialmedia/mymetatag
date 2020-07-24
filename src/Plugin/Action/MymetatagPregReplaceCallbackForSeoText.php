<?php

namespace Drupal\mymetatag\Plugin\Action;


use Drupal\Core\Action\ConfigurableActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Mymetatag preg_replace_callback for SEO text.
 *
 * @Action(
 *   id = "mymetatag_preg_replace_callback_for_seo_text",
 *   label = @Translation("Mymetatag preg_replace_callback for SEO text"),
 *   type = "mymetatag"
 * )
 */
class MymetatagPregReplaceCallbackForSeoText extends ConfigurableActionBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'pattern' => '',
      'callback_body' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['pattern'] = [
      '#type' => 'textfield',
      '#title' => t('Pattern search'),
      '#default_value' => $this->configuration['pattern'],
      '#required' => TRUE,
      '#description' => 'Пример: <em>/&lt;h2&gt;(.*?)&lt;\/h2&gt;/</em>',
    ];
    $form['callback_body'] = [
      '#type' => 'textarea',
      '#title' => 'Тело callback функции',
      '#description' => 'Аргумент - <strong>$matches</strong>, где $matches[0] -  полное вхождение шаблона,
        $matches[1] - вхождение первой подмаски заключенной в круглые скобки и так далее.<br />
        Пример: <em>return \'&lt;h2&gt;\' . strip_tags($matches[1]) . \'&lt;/h2&gt;\';</em>',
      '#default_value' => $this->configuration['callback_body'],
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['pattern'] = $form_state->getValue('pattern');
    $this->configuration['callback_body'] = $form_state->getValue('callback_body');
  }


  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    /** @var \Drupal\mymetatag\MymetatagInterface $entity */
    $seo_text = $entity->getSeoText();
    if ($seo_text) {
      $pattern = $this->configuration['pattern'];
      $callback_body = $this->configuration['callback_body'];
      $new_seo_text = preg_replace_callback($pattern, function ($matches) use ($callback_body) {
        return eval($callback_body);
      }, $seo_text);
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
