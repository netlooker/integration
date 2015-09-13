<?php

/**
 * @file
 * Contains ConfigurationInterface.
 */

namespace Drupal\integration\Configuration;

/**
 * Interface ConfigurationInterface.
 *
 * @package Drupal\integration\Configuration
 */
interface ConfigurationInterface {

  /**
   * Get configuration human readable name.
   *
   * @return string
   *    Configuration name.
   */
  public function getName();

  /**
   * Get plugin name, as returned by hook_integration_PLUGIN_TYPE_info().
   *
   * @return string
   *    Plugin name.
   */
  public function getPlugin();

  /**
   * Get configuration machine name.
   *
   * @return string
   *    Configuration machine name.
   */
  public function getMachineName();

  /**
   * Return a flag indicating whether the backend is enabled.
   *
   * @return int
   *    Flag indicating whether the backend is enabled.
   */
  public function getEnabled();

  /**
   * Return the exportable status of the backend.
   *
   * @return string
   *    Exportable status of the backend.
   *
   * @see integration_configuration_status_options_list()
   */
  public function getStatus();

  /**
   * Check whether the configuration is marked as "Fixed".
   *
   * @return bool
   *    TRUE if condition is met, FALSE otherwise.
   */
  public function isCustom();

  /**
   * Check whether the configuration is marked as "Fixed".
   *
   * @return bool
   *    TRUE if condition is met, FALSE otherwise.
   */
  public function isInCode();

  /**
   * Check whether the configuration is marked as "Fixed".
   *
   * @return bool
   *    TRUE if condition is met, FALSE otherwise.
   */
  public function isOverridden();

  /**
   * Check whether the configuration is marked as "Fixed".
   *
   * @return bool
   *    TRUE if condition is met, FALSE otherwise.
   */
  public function isFixed();

  /**
   * Get value of an entity info array property.
   *
   * @param string $name
   *    Entity key name.
   *
   * @return mixed|bool
   *    Entity key value if set, FALSE otherwise.
   *
   * @see entity_get_info()
   */
  public function getEntityInfoProperty($name);

  /**
   * Get plugin setting value given its name.
   *
   * @param string $name
   *    Plugin setting name.
   *
   * @return mixed|NULL
   *    Plugin setting value if any, NULL otherwise.
   *
   * @see integration_entity_property_info_defaults()
   */
  public function getPluginSetting($name);

  /**
   * Set plugin setting value given its name and value.
   *
   * @param string $name
   *    Plugin setting name.
   * @param mixed $value
   *    Plugin setting value.
   *
   * @see integration_entity_property_info_defaults()
   */
  public function setPluginSetting($name, $value);

  /**
   * Get plugin component setting value given its name.
   *
   * @param string $component
   *    Component name as defined by hook_integration_plugins() implementations.
   * @param string $name
   *    Plugin component setting name.
   *
   * @return mixed|NULL
   *    Plugin component setting value if any, NULL otherwise.
   *
   * @see integration_entity_property_info_defaults()
   */
  public function getComponentSetting($component, $name);

  /**
   * Set component plugin setting value given its name and value.
   *
   * @param string $component
   *    Component name as defined by hook_integration_plugins() implementations.
   * @param string $name
   *    Plugin component setting name.
   * @param mixed $value
   *    Plugin component setting value.
   *
   * @see integration_entity_property_info_defaults()
   */
  public function setComponentSetting($component, $name, $value);

}
