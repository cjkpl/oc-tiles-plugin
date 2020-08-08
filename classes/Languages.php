<?php namespace Cjkpl\Tiles\Classes;

/**
 * Provides lang code to lang name mapping for dropdowns.
 * Only common langs, for easy use, with EN, RU, DE, FR, ES, AR, ZH, PL listed first by default
 */

Class Languages
{
  public static function getCommonLanguageOptions() {
    return array(
      'en' => 'English',
      'ar' => 'Arabic',
      'zh' => 'Chinese (Mandarin)',
      'fr' => 'French',
      'de' => 'German',
      'hi' => 'Hindi',
      'id' => 'Indonesian',
      'jp' => 'Japanese',
      'pl' => 'Polish',
      'pt' => 'Portugese',
      'ru' => 'Russian',
      'es' => 'Spanish',
      'sw' => 'Swahili'
    );
  }
}
