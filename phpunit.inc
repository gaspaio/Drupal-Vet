<?php
/*
 * Adapted (that is shamelessly copied) from drunit module.
 */

/**
 *  We only support PHPUnit > 3.5
 *
 * @return array
 */
function phpunit_version() {

  static $phpunit = NULL;

  if ($phpunit === NULL) {
    if ($path = stream_resolve_include_path("PHPUnit/Autoload.php")) {
      $phpunit = array('path' => $path);

      require_once 'PHPUnit/Runner/Version.php';
      $phpunit['version'] = PHPUnit_Runner_Version::id();
      $phpunit['supported'] = $phpunit['version'] >= '3.5';
    }
  }

  return $phpunit;
}


function dvet_run_tests($suites, $groups = 'All') {

  $return = array();

  // Load base classes and tests, and cache information.
  $classes = dvet_load_classes();

  /*
   * Create our listener : a logger that will store all the necessary
   * information for us to build our report page.
   */
  $listener = new Dvet_Listener();

  // Capture the "console style" output
  ob_start();

  foreach ($classes as $name => $info) {
    if (empty($suites[$name])) {
      continue;
    }

    $suite = new PHPUnit_Framework_TestSuite($name);
    $return['results'][$name] = PHPUnit_TextUI_TestRunner::run($suite, array(
        'backupGlobals' => FALSE,
        'backupStaticAttributes' => FALSE,
        'listeners' => array($listener),
        'colors' => TRUE,
        'processIsolation' => FALSE)
    );
  }

  $return['console'] = ob_get_contents();
  ob_end_clean();

  $return['log'] = $listener->getLog();

  return $return;
}


function dvet_status_pu2d($pu_status) {

  static $phpunit2drupal = array(
    'passed' => 'info',
    'skipped' => 'warning',
    'incomplete' => 'warning',
    'failure' => 'error',
    'error' => 'error',
  );

  $statuses = DVet_Util_Test::getBaseTestRunnerStatuses();
  return $phpunit2drupal[$statuses[$pu_status]];
}


function dvet_load_classes($reset = FALSE, $return = 'classes') {

  static $classes, $groups;

  if ($classes === NULL || $reset) {
    $classes = array();
    $groups = array();

    require_once 'PHPUnit/Autoload.php';

    $module_path = drupal_get_path('module', 'dvet');
    $files = array_reverse(file_scan_directory("$module_path/classes", '/\.php$/'));
    $files += array_reverse(file_scan_directory("$module_path/tests", '/(Test|TestCase)\.php$/'));
    foreach ($files as $file) {
      $pre = get_declared_classes();
      require_once $file->uri;
      $post = get_declared_classes();
      $classes_new = array_values(array_diff($post, $pre));
      foreach ($classes_new as $class) {
        if (dvet_test_extends('DVet_TestCase', $class) && method_exists($class, 'setUp')) {
          $classes[$class] = array(
            'file' => $file->uri,
            'class' => $class,
            'groups' => DVet_Util_Test::getGroups($class),
          );

          foreach ($classes[$class]['groups'] as $group) {
            $groups[$group][$class] = &$classes[$class];
          }
        }
      }
    }
  }

  return ($return != 'groups') ? $classes : $groups;
}


function dvet_load_groups($reset = FALSE) {
  return dvet_load_classes($reset, 'groups');
}


/**
 * Check if a class extends or implements a specific class/interface
 * Test classes' names must end in Test
 *
 * @TODO rewrite this function more inteligently
 *
 * @param $search
 *   The class or interface name to look for
 * @param $className
 *   The class name of the object to compare to
 * @return bool
 */
function dvet_test_extends($search, $className) {
  $class = new ReflectionClass($className);
  if (FALSE === $class
      || $search == $className
      || !preg_match('/.*Test$/', $className)) {
    return FALSE;
  }
  do {
    $name = $class->getName();
    if ($search == $name) {
      return TRUE;
    }
    $class = $class->getParentClass();
  } while (FALSE !== $class);
  return FALSE;
}


