<?php

namespace Drupal\specbee\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\specbee\Services\SpecbeeHelper;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Cache\UncacheableDependencyTrait;

/**
 * Provides Location, Date and Time Block.
 *
 * @Block(
 *   id = "display_location_date_time_block",
 *   admin_label = @Translation("Display Location, Date and Time Block"),
 *   category = @Translation("Custom"),
 * )
 */
class DisplayLocationDateTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  use UncacheableDependencyTrait;

  /**
   * Specbee Helper.
   *
   * @var \Drupal\specbee\Services\SpecbeeHelper
   */
  protected $specbeeHelper;

  /**
   * Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch;

  /**
   * Constructs a Location, Date and Time Block.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\specbee\Services\SpecbeeHelper $specbee_helper
   *   The Specbee Helper service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config Factory Interface.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   The page cache kill switch service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    SpecbeeHelper $specbee_helper,
    ConfigFactoryInterface $config_factory,
    KillSwitch $killSwitch
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->SpecbeeHelper = $specbee_helper;
    $this->config = $config_factory->get('specbee.specbeeconfig');
    $this->killSwitch = $killSwitch;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('specbee.helper'),
      $container->get('config.factory'),
      $container->get('page_cache_kill_switch')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Used to invalidate the cache for anonymous users.
    $this->killSwitch->trigger();
    return [
      '#theme' => 'location_date_time',
      '#country' => $this->config->get('specbee_country') ?? $this->t('India'),
      '#city' => $this->config->get('specbee_city') ?? $this->t('Delhi'),
      '#date_time' => $this->SpecbeeHelper->getCurrentDateTime(),
      '#cache' => [
        'contexts' => ['route'],
        'tags' => $this->config->getCacheTags(),
      ],
    ];
  }

}
