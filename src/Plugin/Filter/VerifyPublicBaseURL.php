<?php
/**
 * CKEditor Public Base URL Check - An alternative to CKEditor "Restrict images to site" filter when using an external
 * domain as public file base URL.
 *
 * @package     ckeditor_public_base_url_check
 * @author      Hongbo He
 */

namespace Drupal\ckeditor_public_base_url_check\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Make sure the src attribute of every <img> element starts with the public file base url.
 *
 * @Filter(
 *   id = "verify_public_base_url",
 *   title = @Translation("Restrict images to public file base URL."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE
 * )
 */

class VerifyPublicBaseURL extends FilterBase {
  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $public_base_url = \Drupal\Core\Site\Settings::get("file_public_base_url");
    $html_dom = Html::load($text);
    $images = $html_dom->getElementsByTagName('img');
    foreach ($images as $image) {
      $src = $image->getAttribute('src');
      if (mb_substr($src, 0, mb_strlen($public_base_url)) !== $public_base_url) {
        \Drupal::moduleHandler()->alter('filter_secure_image', $image);
      }
    }
    $result = new FilterProcessResult();
    $result->setProcessedText(Html::serialize($html_dom));
    return $result;
  }
}
