<?php

namespace Drupal\custom_date_formatter;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Decorates the date formatter service.
 */
class DateFormatterDecorator implements DateFormatterInterface {

  /**
   * The decorated date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $innerDateFormatter;

  /**
   * Constructs a new DateFormatterDecorator.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $inner_date_formatter
   *   The inner date formatter service.
   */
  public function __construct(DateFormatterInterface $inner_date_formatter) {
    $this->innerDateFormatter = $inner_date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function format($timestamp, $type = 'medium', $format = '', $timezone = null, $langcode = null) {
    // Example of adding custom logic before formatting
    if ($type === 'custom_format') {
      // Custom formatting logic
      return "Custom: " . $this->innerDateFormatter->format($timestamp, 'long', $format, $timezone, $langcode);
    }

    // For other cases, use the inner date formatter
    return $this->innerDateFormatter->format($timestamp, $type, $format, $timezone, $langcode);
  }

  /**
   * Implements all other DateFormatterInterface methods by delegating to the inner service.
   */
  public function __call($method, $args) {
    return call_user_func_array([$this->innerDateFormatter, $method], $args);
  }
}
