<?php

/**
 * @name jQuery Update settings
 * @description Check that the jquery_module, is, if installed, correctly configured.
 */
class jQueryUpdate_DVet_Test extends DVet_TestCase {

  /**
   * Do not do anything if jQueryUpdate is not activated.
   */
  protected function setUp() {
    if (!(module_exists('jquery_update'))) {
      $this->markTestSkipped('jQuery Update module is not installed/activated.');
    }
  }

  /**
   * @name jQuery Compression type
   * @description Test if the compressed jQuery script is being served.
   * @failureMessage To save bandwidth, always serve the JS libraries should always be served in their minified version.
   */
  public function testjQueryCompressionType() {
    $this->assertEquals("min", variable_get('jquery_update_compression_type'));
  }

  /**
   * @name jQuery CDN
   * @description Should jQuery be loaded from a local file ?.
   * @failureMessage TODO.
   */
/*  public function testjQueryCDN() {
    $this->assertEquals("none", variable_get('jquery_update_jquery_cdn'));
  }*/
}
