<?php

/**
 * @file
 * Contains \Drupal\integration_producer_ui\ProducerFormHandler.
 */

namespace Drupal\integration_producer_ui;

use Drupal\integration_ui\AbstractFormHandler;
use Drupal\integration_producer\Configuration\ProducerConfiguration;

/**
 * Class ProducerFormHandler.
 *
 * @package Drupal\integration_producer_ui
 */
class ProducerFormHandler extends AbstractFormHandler {

  /**
   * {@inheritdoc}
   */
  public function form(array &$form, array &$form_state, $op) {
    /** @var ProducerConfiguration $configuration */
    $configuration = $this->getConfiguration();
    $plugin = '';
    $entity_type = '';

    // Build plugin type form portion.
    $this->buildPluginForm($form, $form_state, $op);

    // Select entity bundle based on producer plugin type.
    if ($plugin = $configuration->getPlugin()) {
      $this->buildEntityBundleForm($form, $form_state, $op);
    }

    // Add field mapping form portion.
    $entity_type = $this->getPluginManager()->getEntityType($plugin);
    $entity_bundle = $configuration->getEntityBundle();
    if ($entity_type && $entity_bundle) {
      $this->buildFieldMappingForm($form, $form_state, $op);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function formSubmit(array $form, array &$form_state) {
    /** @var ProducerConfiguration $configuration */
    $configuration = $this->getConfiguration();
    $input = &$form_state['input'];
    $triggering_element = $form_state['triggering_element'];

    switch ($triggering_element['#name']) {

      // Add field to plugin settings.
      case 'plugin_submit':
      case 'entity_bundle_submit':
        $form_state['rebuild'] = TRUE;
        break;

      // Remove field from plugin settings.
      case 'remove-field':
        $configuration->unsetPluginSetting($triggering_element['#field']);
        $form_state['rebuild'] = TRUE;
        break;
    }

    // Remove UI-related values from plugin settings.
    foreach (array('fields') as $name) {
      unset($configuration->settings[$name]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function formValidate(array $form, array &$form_state) {

  }

  /**
   * Helper function: render entity bundle form portion.
   *
   * @param array $form
   *    Form array.
   * @param array $form_state
   *    Form state array.
   * @param string $op
   *    Current form operation.
   */
  protected function buildPluginForm(array &$form, array &$form_state, $op) {
    /** @var ProducerConfiguration $configuration */
    $configuration = $this->getConfiguration();

    // Select producer plugin type.
    $options = $this->getPluginManager()->getFormOptions();
    $form['plugin_container'] = array(
      '#type' => 'fieldset',
      '#title' => t('Producer plugin'),
      '#tree' => FALSE,
      '#attributes' => array('class' => array('container-inline')),
    );
    $form['plugin_container']['plugin'] = array(
      '#type' => 'select',
      '#title' => t('Producer plugin'),
      '#title_display' => 'invisible',
      '#options' => $options,
      '#default_value' => $configuration->getPlugin(),
      '#required' => TRUE,
    );
    $form['plugin_container']['plugin_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Select plugin'),
      '#name' => 'plugin_submit',
      '#limit_validation_errors' => array(),
      '#submit' => array('integration_ui_entity_form_submit'),
    );
  }


  /**
   * Helper function: render entity bundle form portion.
   *
   * @param array $form
   *    Form array.
   * @param array $form_state
   *    Form state array.
   * @param string $op
   *    Current form operation.
   */
  protected function buildEntityBundleForm(array &$form, array &$form_state, $op) {
    /** @var ProducerConfiguration $configuration */
    $configuration = $this->getConfiguration();
    $plugin = $configuration->getPlugin();

    $entity_type = $this->getPluginManager()->getEntityType($plugin);
    $entity_info = entity_get_info($entity_type);
    $options = $this->extractSelectOptions($entity_info['bundles'], 'label');
    $entity_bundle = $configuration->getEntityBundle();

    $form['entity_bundle_container'] = array(
      '#type' => 'fieldset',
      '#title' => t('Entity bundle'),
      '#tree' => FALSE,
      '#attributes' => array('class' => array('container-inline')),
    );
    $form['entity_bundle_container']['entity_bundle'] = array(
      '#type' => 'select',
      '#title' => t('Entity bundle'),
      '#title_display' => 'invisible',
      '#options' => $options,
      '#default_value' => $entity_bundle,
      '#required' => TRUE,
    );
    $form['entity_bundle_container']['entity_bundle_submit'] = array(
      '#type' => 'submit',
      '#value' => t('Select bundle'),
      '#name' => 'entity_bundle_submit',
      '#limit_validation_errors' => array(),
      '#submit' => array('integration_ui_entity_form_submit'),
    );
  }

  /**
   * Helper function: render field mapping form portion.
   *
   * @param array $form
   *    Form array.
   * @param array $form_state
   *    Form state array.
   * @param string $op
   *    Current form operation.
   */
  protected function buildFieldMappingForm(array &$form, array &$form_state, $op) {
    /** @var ProducerConfiguration $configuration */
    $configuration = $this->getConfiguration();
    $entity_bundle = $configuration->getEntityBundle();

    /** @var \EntityDrupalWrapper $entity_wrapper */
    $entity_wrapper = entity_metadata_wrapper('node');
    $properties = $entity_wrapper->refPropertyInfo();
    $options = $this->extractSelectOptions($properties['bundles'][$entity_bundle]['properties'], 'label');
    $options += $this->extractSelectOptions($properties['properties'], 'label');
    asort($options);

    $form['settings'] = array(
      '#tree' => TRUE,
    );
    $rows = array();
    $form['settings']['plugin'] = array(
      '#tree' => FALSE,
    );
    $mapping = (array) $configuration->getPluginSetting('mapping');

    foreach ($mapping as $source => $destination) {
      $form['settings']['plugin']['mapping'][$source] = array(
        '#value' => $destination,
      );
      $row = array();
      $row['source'] = array('#markup' => $source);
      $row['destination'] = array('#markup' => $destination);
      $row['remove_mapping_submit'] = array(
        '#type' => 'submit',
        '#value' => t('Remove'),
        '#name' => 'remove_mapping_submit',
        '#field' => $source,
        '#limit_validation_errors' => array(),
        '#submit' => array('integration_ui_entity_form_submit'),
      );
      $rows[] = $row;
    }

    $rows[] = array(
      'new_source' => array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => '',
      ),
      'field_label' => array(
        '#type' => 'textfield',
        '#default_value' => '',
      ),
      'add_mapping_submit' => array(
        '#type' => 'submit',
        '#value' => t('Add mapping'),
        '#name' => 'add_mapping_submit',
        '#limit_validation_errors' => array(),
        '#submit' => array('integration_ui_entity_form_submit'),
      ),
    );

    $header = array(t('Source'), t('Destination'), '');
    $form['mapping'] = array(
      '#theme' => 'integration_form_table',
      '#header' => $header,
      '#tree' => FALSE,
      'rows' => $rows,
    );
  }

}
