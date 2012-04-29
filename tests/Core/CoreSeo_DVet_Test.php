<?php

/**
 * @name Core SEO settings
 * @description Drupal 7 core SEO related settings.
 * @group seo
 */
class CoreSEO_DVet_Test extends DVet_TestCase {

  protected function setUp() {}

  /**
   * @name Clean URLs
   * @description TODO DESCRIPTION.
   * @passedMessage Clean URLs are activated. Great !
   * @failureMessage Clean URLs should always be activated.
   */
  public function testCleanURL() {
    $this->assertTrue((bool) variable_get('clean_url'));
  }

  //TODO we should check if the apache config allows this but, what if we have a nginx config ?
}
