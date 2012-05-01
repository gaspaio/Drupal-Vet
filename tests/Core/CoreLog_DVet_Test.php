<?php

/**
 * @name D7 Core log settings
 * @description Several drupal core log related settings.
 */
class CoreLogs_DVet_Test extends DVet_TestCase {

  protected function setUp() {}

  /**
   * @name DbLog
   * @failureMessage In a production site, the DBLog should be deactivated. Logging should be performed via syslog, por example.
   * @group performance
   */
  public function testDBLog() {
    $this->assertFALSE(module_exists('dblog'));
  }

  /**
   * @name Error display
   * @failureMessage In a production site, errors should never be displayed.
   * @group security
   */
  public function testErrorDisplay() {
    $this->assertEquals(ERROR_REPORTING_HIDE, variable_get('error_level', ERROR_REPORTING_DISPLAY_ALL));
  }
}
