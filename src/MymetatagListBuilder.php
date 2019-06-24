<?php

namespace Drupal\mymetatag;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * Provides a list controller for mymetatag entity.
 * @ingroup mymetatag
 */
class MymetatagListBuilder extends EntityListBuilder {

  
  /**
   * {@inheritdoc}
   */
  public function render() {
    $link = Link::createFromRoute('метатеги по шаблонам', 'entity.myroute_metatag.collection');
    $link = $link->toString();
    $build['description'] = [
      '#markup' => '<p>Метатеги для конкретной страницы имеют больший приоритет чем ' . $link . ', 
                    поэтому они будут использованы даже если есть шаблон для этих страниц.<br />
                    Для страницы можно указать: <strong>h1, тайтл, дескрипшен, сео-текст, noindex</strong></p>',
    ];
    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['path'] = $this->t('Path');
    $header['title_h1'] = $this->t('H1');
    $header['head_title'] = $this->t('Head title');
    $header['description'] = $this->t('Description');
    $header['seo_text'] = $this->t('Seo text');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $mymetatag) {
    /* @var $mymetatag \Drupal\mymetatag\Entity\Mymetatag */
    $row['id'] = $mymetatag->id();
    $source_path = $mymetatag->getSourcePath();
    $url = Url::fromUserInput($source_path);
    $path_link = Link::fromTextAndUrl($source_path, $url);
    $row['path'] = $path_link;
    $row['title_h1'] = $this->trimRow($mymetatag->getTitleH1());
    $row['head_title'] = $this->trimRow($mymetatag->getHeadTitle());
    $row['description'] = $this->trimRow($mymetatag->getDescription());
    $row['seo_text'] = $this->trimRow($mymetatag->getSeoText());
    return $row + parent::buildRow($mymetatag);
  }


  private function trimRow($str) {
    $str = strip_tags($str);
    $str = trim($str);
    $par['max_length'] = 25; // количество символов
    $par['word_boundary'] = TRUE; // обрезать только целые слова
    $par['ellipsis'] = TRUE; // добавить многоточие
    $par['html'] = TRUE; // строка может содержать html
    return FieldPluginBase::trimText($par, $str);
  }

}
