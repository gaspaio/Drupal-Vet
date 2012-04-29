<?php

function dvet_admin_form($form, &$form_state) {

  $defaults = variable_get('dvet_settings_test_suites', array());

  $options = array();
  foreach (dvet_load_classes() as $class => $info) {
    $options[$class] = array(
      'title' => DVet_Util_Test::getName($class),
      'class' => $class,
      'description' => DVet_Util_Test::getDescription($class),
      // Add group information.
    );
  }

  $headers = array(
    'title' => t('Test suite'),
    'class' => t('Class'),
    'description' => t('Description'),
  );

  $form['dvet_settings_test_suites'] = array(
    '#type' => 'tableselect',
    '#header' => $headers,
    '#options' => $options,
    '#empty' => t('No test suites available'),
    '#default_value' => $defaults,
  );

  return system_settings_form($form);
}
