<?php

/**
 * TODO Replace the listener by theme functions using the PU result object directly.
 */

class Dvet_Listener implements PHPUnit_Framework_TestListener {

  private $log;

  public function __construct() {
    $this->log = array();
  }

  /*
   * PHPUnit_Framework_TestListener methods
   */
  public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
    $this->logTestResult($test, $e);
    $test->dvet_listener = TRUE;
  }

  public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
    $this->logTestResult($test, $e);
    $test->dvet_listener = TRUE;
  }

  public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    $this->logTestResult($test, $e);
    $test->dvet_listener = TRUE;
  }

  public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    // Status seems to be NULL for skipped tests.
    if ($test->getStatus() === NULL) {
      $this->logTestResult($test, $e, PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED);
    }
    else {
      $this->logTestResult($test, $e);
    }
    $test->dvet_listener = TRUE;
  }

  public function startTest(PHPUnit_Framework_Test $test) {
  }

  public function endTest(PHPUnit_Framework_Test $test, $time) {
    // Do not log non passed tests twice.
    if (empty($test->dvet_listener)) {
      $this->logTestResult($test);
    }
  }

  public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
    $this->logSuite($suite);
  }

  public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
  }

  public function getLog() {
    return $this->log;
  }

  public function reset() {
    return $this->log = array();
  }

  private function logSuite($suite) {
    $this->log[$suite->getName()] = array(
      'name' => DVet_Util_Test::getName($suite->getName()),
      'description' => DVet_Util_Test::getDescription($suite->getName()),
      'tests' => array(),
    );
  }

  private function logTestResult($test, $e = NULL, $status = NULL) {
    // To create a reflector just to get the classname is a bit too much ...
    $className = get_class($test);
    $methodName = $test->getName();

    if (isset($this->log[$className]['tests'][$methodName])) {
      return;
    }

    $this->log[$className]['tests'][$methodName] = array(
      'name' => DVet_Util_Test::getName($className, $methodName),
      'description' => DVet_Util_Test::getDescription($className, $methodName),
      'status' => isset($status) ? $status : $test->getStatus(),
    );

    if (!($msg = DVet_Util_Test::getStatusMessage(
      $this->log[$className]['tests'][$methodName]['status'],
      $className,
      $methodName))) {

      if (!($msg = $test->getStatusMessage()) && $e) {
        $msg = $e->getMessage();
      }
    }

    $this->log[$className]['tests'][$methodName]['message'] = $msg;
  }
}
