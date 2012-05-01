<?php

/**
 * @name Core SEO settings
 * @description Drupal 7 core SEO related settings.
 */
class CoreMisc_DVet_Test extends DVet_TestCase {

  protected function setUp() {}

  /**
   * @name Clean URLs
   * @description TODO DESCRIPTION.
   * @group seo
   * @failureMessage Clean URLs should always be activated.
   */
  public function testCleanURL() {
    $this->assertTrue((bool) variable_get('clean_url'));
  }



  /**
   * @name Release notes & help files
   * @description Check if the text files that come with Drupal core have been removed.
   * @group security
   * @failureMessage Files like /CHANGELOG.txt, etc should be removed from production sites.
   * @see This test was adapted from the prod_check module.
   */
  public function testTXTFilesExist() {
    $files = array(
      'CHANGELOG.txt',
      'COPYRIGHT.txt',
      'INSTALL.mysql.txt',
      'INSTALL.pgsql.txt',
      'INSTALL.sqlite.txt',
      'INSTALL.txt',
      'LICENSE.txt',
      'MAINTAINERS.txt',
      'README.txt',
      'UPGRADE.txt',
      'sites/example.sites.php',
      'sites/all/README.txt',
      'sites/all/themes/README.txt',
      'sites/all/modules/README.txt',
    );

    $remaining_files = array();
    foreach ($files as $file) {
      // It would seem that $_SERVER['DOCUMENT_ROOT'] is not always set, hence the
      // use of realpath() in combination with base path to determine the full
      // path to the Drupal installation.
      if (file_exists(realpath('.'.base_path()).'/'.$file)) {
        array_push($remaining_files, $file);
      }
    }

    $this->assertEmpty($remaining_files);
  }
}
