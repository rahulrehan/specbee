<?php

namespace Drupal\specbee\Services;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class SpecbeeHelper.
 *
 * Specbee Helper class.
 *
 * @package Drupal\specbee\Services
 */
class SpecbeeHelper {

  /**
   * Date Formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * A date time instance.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  private $time;

  /**
   * Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a Specbee Helper Service.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   A date time instance.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config Factory Interface.
   */
  public function __construct(
    DateFormatter $date_formatter,
    TimeInterface $time,
    ConfigFactoryInterface $config_factory
  ) {
    $this->dateFormatter = $date_formatter;
    $this->time = $time;
    $this->config = $config_factory->get('specbee.specbeeconfig');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('datetime.time'),
      $container->get('config.factory'),
    );
  }

  /**
   * Provides current date and time.
   *
   * @return string
   *   Returns data and time.
   */
  public function getCurrentDateTime() {
    // Return Data & Time in the format like 25th Oct 2019 - 10:30 PM.
    return $this->dateFormatter->format(
      $this->time->getCurrentTime(),
      'custom',
      'dS M Y - g:i A',
      $this->config->get('specbee_timezone') ?? NULL
    );
  }

}
