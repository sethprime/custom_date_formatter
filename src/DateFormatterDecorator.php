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
   * {@inheritdoc}
   */
  public function formatInterval($interval, $granularity = 2, $langcode = NULL)
  {
    return $this->innerDateFormatter->formatInterval($interval, $granularity, $langcode);
  }

  /**
   * {@inheritdoc}
   */
  public function getSampleDateFormats($langcode = NULL, $timestamp = NULL, $timezone = NULL)
  {
    return $this->innerDateFormatter->getSampleDateFormats($langcode, $timestamp, $timezone);
  }

  /**
   * {@inheritdoc}
   */
  public function formatTimeDiffUntil($timestamp, $options = [])
  {
    return $this->innerDateFormatter->formatTimeDiffUntil($timestamp, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function formatTimeDiffSince($timestamp, $options = [])
  {
    return $this->innerDateFormatter->formatTimeDiffSince($timestamp, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function formatDiff($from, $to, $options = [])
  {
    return $this->innerDateFormatter->formatDiff($from, $to, $options);
  }
}
