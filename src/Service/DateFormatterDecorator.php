<?php

namespace Drupal\custom_date_formatter\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\custom_date_formatter\Service\DateReplacementService;

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
   * The custom date formatter service.
   *
   * @var \Drupal\custom_date_formatter\Service\DateReplacementService
   */
  protected $customFormatter;

  /**
   * Constructs a new DateFormatterDecorator.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $inner_date_formatter
   *   The inner date formatter service.
   *
   * @param \Drupal\custom_date_formatter\Service\DateReplacementService $custom_formatter
   *   The custom date formatter service.
   */
  public function __construct(DateFormatterInterface $inner_date_formatter,
    DateReplacementService $custom_formatter) {
    $this->innerDateFormatter = $inner_date_formatter;
    $this->customFormatter = $custom_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public function format($timestamp, $type = 'medium', $format = '', $timezone = null, $langcode = null) {
    return $this->customFormatter->format($timestamp, $type, $format, $timezone, $langcode);
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
