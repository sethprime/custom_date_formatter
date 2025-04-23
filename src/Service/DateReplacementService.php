<?php

namespace Drupal\date_formatter_vedic\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Service for handling date replacements.
 */
class DateReplacementService {

  /**
   * The decorated date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $innerDateFormatter;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Language manager for retrieving default langcode when none is specified.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Entity Type Manager to retrive date format pattern.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new DateReplacementService.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $inner_date_formatter
   *   The inner date formatter service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *  The language manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager_interface
   *   The entity storage manager.
   */
  public function __construct(DateFormatterInterface $inner_date_formatter,
    Configfactoryinterface $config_factory,
    LanguageManagerInterface $language_manager,
    EntityTypeManagerInterface $entity_type_manager_interface) {
    $this->innerDateFormatter = $inner_date_formatter;
    $this->configFactory = $config_factory;
    $this->languageManager = $language_manager;
    $this->entityTypeManager = $entity_type_manager_interface;
  }

  /**
   * Get the character that should be replaced in date strings.
   *
   * @return string
   *   The character to replace.
   */
  public function getCharacterToReplace() {
    return $this->configFactory->get('date_formatter_vedic.settings')->get('replacement_character') ?: '@';
  }

  /**
   * Calculate replacement string based on the datetime object.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   The date object.
   *
   * @return string
   *   The replacement string.
   */
  public function calculateReplacement(DrupalDateTime $date) {
    $config = $this->configFactory->get('date_formatter_vedic.settings');
    $muhurtas = $config->get('muhurtas') ?: [];

    // Get the DateTimeZone object.
    $timezone = $date->getTimezone();

    // Get the latitude and longitude of the timezone.
    $timezone_location = $timezone->getLocation();

    // Get the sunrise time.
    $sun_info = date_sun_info($date->getTimestamp(), $timezone_location['latitude'], $timezone_location['longitude']);

    $seconds_from_sunrise = $date->getTimestamp() - $sun_info['sunrise'];
    $current_muhurta_number = floor($seconds_from_sunrise / 60 / 48);

    $muhurta = $muhurtas[$current_muhurta_number] ?: '';

    return $muhurta;
  }

  /**
   * Gets a date format pattern by its machine name.
   *
   * @param string $formatName
   *   The date format machine name.
   *
   * @return string|null
   *   The pattern or NULL if the format doesn't exist.
   */
  public function getDateFormatPattern($formatName) {
    try {
      $dateFormat = $this->entityTypeManager->getStorage('date_format')->load($formatName);
      return $dateFormat ? $dateFormat->getPattern() : NULL;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Splits a string by a delimiter and includes the delimiters in the result.
   *
   * Similar to PHP's native explode() function but with the enhancement of
   * preserving the delimiter characters in the resulting array. This is useful
   * when the separators themselves are meaningful and need to be processed.
   *
   * @param string $delimiter
   *   The boundary string used for splitting.
   * @param string $string
   *   The input string to be split.
   *
   * @return array
   *   An array containing the parts of the string with the delimiters included
   *   as separate elements.
   *
   * @example
   *   $result = explodeWithSeparators(',', 'one,two,three');
   *   // Result: ['one', ',', 'two', ',', 'three']
   */
  function explodeWithSeparators($delimiter, $string) {
    // If the string is empty, return an empty array
    if (empty($string)) {
        return [];
    }

    // If the delimiter is empty, return the string as a single element array
    if (empty($delimiter)) {
        return [$string];
    }

    $result = [];
    $position = 0;
    $delimiterLength = strlen($delimiter);

    // Find each occurrence of the delimiter
    while (($nextPos = strpos($string, $delimiter, $position)) !== false) {
        // Add the substring before the delimiter
        if ($nextPos > $position) {
            $result[] = substr($string, $position, $nextPos - $position);
        }

        // Add the delimiter itself
        $result[] = $delimiter;

        // Move position after this delimiter
        $position = $nextPos + $delimiterLength;
    }

    // Add the remaining part of the string if any
    if ($position < strlen($string)) {
        $result[] = substr($string, $position);
    }

    return $result;
  }

  /**
   * Formats a date, using a date type or a custom date format string.
   *
   * Drupal uses the PHP date() and format_date() system for formatting dates.
   * This method provides a standardized way to format dates across the platform.
   *
   * @param int $timestamp
   *   A UNIX timestamp to format.
   * @param string $type
   *   (optional) The format to use, one of:
   *   - A predefined date format string name ('short', 'medium', 'long').
   *   - The name of a custom date format, defined by a date format config entity.
   *   - 'custom', to use $format.
   *   Defaults to 'medium'.
   * @param string $format
   *   (optional) If $type is 'custom', a PHP date format string suitable for
   *   input to date(). Use a backslash to escape ordinary text, so it does not
   *   get interpreted as date format characters.
   * @param string|null $timezone
   *   (optional) Time zone identifier, as described at
   *   https://www.php.net/manual/en/timezones.php. If NULL, the configured
   *   site timezone is used.
   * @param string|null $langcode
   *   (optional) Language code to translate to. NULL (default) means to use
   *   the user interface language.
   *
   * @return string
   *   A translated date string in the requested format.
   */
  public function format($timestamp, $type = 'medium', $format = '', $timezone = null, $langcode = null) {
    if (!isset($timezone)) {
        $timezone = date_default_timezone_get();
    }
    $timezone_object = timezone_open($timezone);

    if (empty($langcode)) {
        $langcode = $this->languageManager
            ->getCurrentLanguage()
            ->getId();
    }

    $datetime_settings = [
      'langcode' => $langcode,
    ];

    // Create a DrupalDateTime object from the timestamp and timezone.
    $date = DrupalDateTime::createFromTimestamp($timestamp, $timezone_object, $datetime_settings);

    // If we have a non-custom date format use the provided date format pattern.
    if ($type !== 'custom') {
      $format = $this->getDateFormatPattern($type);
    }

    $char_to_replace = $this->getCharacterToReplace();
    $replacement = $this->calculateReplacement($date);

    $format_parts = $this->explodeWithSeparators($char_to_replace, $format);
    $formatted_date = '';

    // Process the date format piecewise.
    foreach($format_parts as $date_segment) {
      if ($date_segment == $char_to_replace) {
        $formatted_date .= $replacement;
      } else {
        $formatted_date .= $this->innerDateFormatter->format($timestamp, 'custom', $date_segment, $timezone, $langcode);
      }
    }

    return $formatted_date;
  }
}
