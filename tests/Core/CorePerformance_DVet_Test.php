<?php

/**
 * @name Core performance settings
 * @description Drupal 7 core performance settings.
 * @group performance
 */
class CorePerfomance_DVet_Test extends DVet_TestCase {

  protected function setUp() {}

  /**
   * @name Page cache
   * @description Serving cached pages for to anonymous users.
   * @passedMessage Page cache is activated. Great !
   * @failureMessage Page cache should always be activated, even with a very short lifetime.
   */
  public function testPageCache() {
    $this->assertEquals(variable_get('cache'), 1);
  }

  /**
   * @name Page cache lifetime
   * @description Cache lifetime determines for how long cached pages will be served from cache.
   * @depends testPageCache
   * @failureMessage Cache lifetime shoud be greated than 0 for pages to be served from cache. If a tiny lifetime is better than no lifetime.
   */
  public function testCacheLifetime() {
    $this->assertGreaterThan(0, (int) variable_get('cache_lifetime', 0));
  }

  /**
   * @name Block cache
   * @description Block cache description.
   * @failureMessage Error message from block cache
   * @notes
   * - User 1 is always excluded from block cache () (block module:912)
   * - Also, block caching is incompatible with implementations of hook_node_grants (block.module:849)
   */
  public function testBlockCache() {
    $this->assertEquals(variable_get('block_cache'), 1);
  }

  /**
   * @name Page cache maximum age
   * @description External cache page expiration date should always be set to something greater than 0 for Drupal to set the max-age http header and thus, allow for external caching.
   * @failureMessage Set the 'Expiration of cached pages' to something greater than 0.
   * @notes
   * - see bootstrap:1330
   */
  public function testExpirationOfCachedPages() {

    $this->assertGreaterThan(0, variable_get('page_cache_maximum_age', 0));
  }

  // Page compression ? It depends on server configuration.

  /**
   * @name Preprocess CSS
   * @description TODO Describe this.
   * @failureMessage Error message from preprocess css
   */
  public function testPreprocessCSS() {
    $this->assertTrue(variable_get('preprocess_css'));
  }

  /**
   * @name Preprocess JS
   * @description TODO Describe this.
   * @failureMessage Error message from preprocess js
   */
  public function testPreprocessJS() {
    $msg = "Error message from preprocess js";
    $this->assertTrue(variable_get('preprocess_js'), $msg);
  }
}
