<?php

function theme_dvet_results($variables) {

  $html = '<h2>' . t('Drupal Vet Report') . '</h2>';

  // TODO Display settings summary for this report

  // Render the execution log
  $html .= theme('dvet_log', array(
    'log' => $variables['results']['log']));

  if ($variables['results']['run_settings']['display_console']) {
    $console_vars = array(
      'title' => t('PHPUnit text output'),
      'output' => $variables['results']['console'],
    );

    $html .= theme('dvet_console', $console_vars);
  }

  return $html;
}


function theme_dvet_log($variables) {

  static $pu2requirement = array(
    'passed' => REQUIREMENT_OK,
    'skipped' => REQUIREMENT_INFO,
    'incomplete' => REQUIREMENT_WARNING,
    'failure' => REQUIREMENT_ERROR,
    'error' => REQUIREMENT_ERROR,
  );

  $pu = DVet_Util_Test::getBaseTestRunnerStatuses();
  $run_settings = _dvet_run_settings();

  $status_report_items = array();
  foreach ($variables['log'] as $class => $class_info) {
    foreach ($class_info['tests'] as $test => $test_info) {

      if (!in_array($test_info['status'], $run_settings['display_statuses'])
          || $run_settings['display_statuses'][$test_info['status']] === 0) {
        continue;
      }

      $item = array();
      $item['severity'] = $pu2requirement[$pu[$test_info['status']]];
      $item['title'] = $test_info['name'];
      $item['title'] .= ': <em>' . ucwords($pu[$test_info['status']]) . '</em>';
      $item['value'] = $test_info['message'];
      if (!empty($test_info['description'])) {
        $item['description'] = $test_info['description'];
      }

      $status_report_items[] = $item;
    }
  }

  $vars = array('requirements' => $status_report_items);
  return theme('status_report', $vars);
}


function theme_dvet_console($variables) {

  $fieldset = array(
    '#type' => 'fieldset',
    '#title' => $variables['title'],
    '#value' => "<pre>" . $variables['output'] . "</pre>",
    '#attributes' => array('class' => array('dvet-results-console')),
  );

  // Manually include collapsible JS since we're printing a fieldset outside a form.
  $fieldset['#attached']['library'][] = array('system', 'drupal.collapse');
  $fieldset['#attributes']['class'][] = 'collapsible';
  $fieldset['#attributes']['class'][] = 'collapsed';

  return render($fieldset);
}



function theme_dvet_report_run($variables) {

  static $phpunit2drupal = array(
    'passed' => 'status',
    'skipped' => 'warning',
    'incomplete' => 'warning',
    'failure' => 'error',
    'error' => 'error',
  );

  $statuses = DVet_Util_Test::getBaseTestRunnerStatuses();
  $msg_class = $phpunit2drupal[$statuses[$variables['status']]];

  $html = '<h4 class="title">' . $variables['name'] . '</h4>';
  $statusstr = t('Status: <em>@status</em>', array(
    '@status' => ucwords($statuses[$variables['status']])));

  $html .= '<p class="test-status">' . $statusstr . '</p>';

  $html .= '<p class="test-message">' . $variables['message'] . '</p>';

  $html .= '<p class="test-id">' . $variables['class'] . '::' . $variables['method'] . '</p>';

  return '<div class="dvet-report-item messages ' . $msg_class . '">' . $html . '</div>';
}


function theme_dvet_report_group($variables) {

  $phpunit2drupal = array(
    'passed' => 'status',
    'skipped' => 'warning',
    'incomplete' => 'warning',
    'failure' => 'error',
    'error' => 'error',
  );

  $statuses = DVet_Util_Test::getBaseTestRunnerStatuses();

  foreach ($variables['log'] as $suite_name => $suite_log) {
    foreach ($suite_log['tests'] as $test_name => $test_info) {


      $test_info['class'] = $suite_name;
      $test_info['method'] = $test_name;
      $content .= theme('dvet_report_item', $test_info);
    }
  }


  // @todo
  //drupal_add_js(drupal_get_path('module', 'security_review') . '/security_review.js', array('scope' => 'footer'));
  $output = '<h3>' . $variables['header'] . '</h3>';
  $output .= '<table class="system-status-report">';
  if (!empty($variables['items'])) {
    foreach ($variables['items'] as $item) {
      $output .= '<tr class="' . $item['class'] . '">';
      $output .= '<td class="status-icon"><div title="' . $item['title'] . '"><span class="element-invisible">' . $item['title'] . '</span></div></td>';
      $output .= '<td>' . $item['message'] . '</td>';
      $output .= '<td>' . $item['help_link'] . '</td>';
      $output .= '<td>' . $item['toggle_link'] . '</td>';
      $output .= '</tr>';
    }
  }
  $output .= '</table>';
  return $output;
}





