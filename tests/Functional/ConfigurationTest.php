<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_theme\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that theme configuration is correctly applied.
 */
class ConfigurationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'config',
    'system',
    'oe_theme_helper',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Enable and set OpenEuropa Theme as default.
    $this->container->get('theme_installer')->install(['oe_theme']);
    $this->container->get('theme_handler')->setDefault('oe_theme');
    $this->container->set('theme.registry', NULL);
  }

  /**
   * Test that the the default settings are applied correctly.
   */
  public function testDefaultSettings(): void {
    $this->drupalGet('<front>');

    // Assert that we load the EC component library by default.
    $this->assertLinkContainsHref('/oe_theme/dist/ec/styles/ecl-ec-preset-legacy-website.css');
    $this->assertLinkContainsHref('/oe_theme/css/style-ec.css');

    $this->assertScriptContainsSrc('/oe_theme/dist/ec/scripts/ecl-ec-preset-legacy-website.js');
    $this->assertScriptContainsSrc('/oe_theme/js/ecl_auto_init.js');

    // Assert that we do not load the EU component library by default.
    $this->assertLinkNotContainsHref('/oe_theme/dist/eu/styles/ecl-eu-preset-legacy-website.css');
    $this->assertLinkNotContainsHref('/oe_theme/css/style-eu.css');

    $this->assertScriptNotContainsSrc('/oe_theme/dist/eu/scripts/ecl-eu-preset-legacy-website.js');

    // Assert that the ECL Editor preset is always loaded.
    $this->assertLinkContainsHref('/oe_theme/dist/styles/ecl-ec-preset-editor.css');
  }

  /**
   * Test that the correct library is loaded after changing theme settings.
   */
  public function testChangeComponentLibrary(): void {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    // Create a user that does have permission to administer theme settings.
    $user = $this->drupalCreateUser(['administer themes']);
    $this->drupalLogin($user);

    // Visit theme administration page.
    $this->drupalGet('/admin/appearance/settings/oe_theme');

    // Assert configuration select is properly rendered.
    $assert_session->selectExists('Component library');
    $assert_session->optionExists('Component library', 'European Commission');
    $assert_session->optionExists('Component library', 'European Union');

    // Select EU component library and save configuration.
    $page->selectFieldOption('Component library', 'European Union');
    $page->pressButton('Save configuration');

    // Visit font page.
    $this->drupalGet('<front>');

    // Assert that we load the EU component library.
    $this->assertLinkContainsHref('/oe_theme/dist/eu/styles/ecl-eu-preset-legacy-website.css');
    $this->assertLinkContainsHref('/oe_theme/css/style-eu.css');

    $this->assertScriptContainsSrc('/oe_theme/dist/eu/scripts/ecl-eu-preset-legacy-website.js');
    $this->assertScriptContainsSrc('/oe_theme/js/ecl_auto_init.js');

    // Assert that we do not load the EC component library.
    $this->assertLinkNotContainsHref('/oe_theme/dist/ec/styles/ecl-ec-preset-legacy-website.css');
    $this->assertLinkNotContainsHref('/oe_theme/css/style-ec.css');

    $this->assertScriptNotContainsSrc('/oe_theme/dist/ec/scripts/ecl-ec-preset-legacy-website.js');

    // Assert that the ECL Editor preset is always loaded.
    $this->assertLinkContainsHref('/oe_theme/dist/styles/ecl-ec-preset-editor.css');

    // Visit theme administration page.
    $this->drupalGet('/admin/appearance/settings/oe_theme');

    // Select EC component library and save configuration.
    $page->selectFieldOption('Component library', 'European Commission');
    $page->pressButton('Save configuration');

    // Visit font page.
    $this->drupalGet('<front>');

    // Assert that we load the EC component library by default.
    $this->assertLinkContainsHref('/oe_theme/dist/ec/styles/ecl-ec-preset-legacy-website.css');
    $this->assertLinkContainsHref('/oe_theme/css/style-ec.css');

    $this->assertScriptContainsSrc('/oe_theme/dist/ec/scripts/ecl-ec-preset-legacy-website.js');
    $this->assertScriptContainsSrc('/oe_theme/js/ecl_auto_init.js');

    // Assert that we do not load the EU component library by default.
    $this->assertLinkNotContainsHref('/oe_theme/dist/eu/styles/ecl-eu-preset-legacy-website.css');
    $this->assertLinkNotContainsHref('/oe_theme/css/style-eu.css');

    $this->assertScriptNotContainsSrc('/oe_theme/dist/eu/scripts/ecl-eu-preset-legacy-website.js');

    // Assert that the ECL Editor preset is always loaded.
    $this->assertLinkContainsHref('/oe_theme/dist/styles/ecl-ec-preset-editor.css');
  }

  /**
   * Assert that current response contians a link tag with given href.
   *
   * @param string $href
   *   Partial content of the href attribute.
   */
  protected function assertLinkContainsHref(string $href): void {
    $this->assertSession()->responseMatches('<link .*href=\".*' . preg_quote($href) . '\?\w+\" \/>');
  }

  /**
   * Assert that current response does not contian a link tag with given href.
   *
   * @param string $href
   *   Partial content of the href attribute.
   */
  protected function assertLinkNotContainsHref(string $href): void {
    $this->assertSession()->responseNotMatches('<link .*href=\".*' . preg_quote($href) . '\?\w+\" \/>');
  }

  /**
   * Assert that current response contians a script tag with given src.
   *
   * @param string $src
   *   Partial content of the src attribute.
   */
  protected function assertScriptContainsSrc(string $src): void {
    $this->assertSession()->responseMatches('<script .*src=\".*' . preg_quote($src) . '\?\w+\">');
  }

  /**
   * Assert that current response doe not contian a script tag with given src.
   *
   * @param string $src
   *   Partial content of the src attribute.
   */
  protected function assertScriptNotContainsSrc(string $src): void {
    $this->assertSession()->responseNotMatches('<script .*src=\".*' . preg_quote($src) . '\?\w+\">');
  }

}
