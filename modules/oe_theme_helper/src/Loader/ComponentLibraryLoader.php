<?php

declare(strict_types = 1);

namespace Drupal\oe_theme_helper\Loader;

use Drupal\Core\Extension\ThemeHandler;
use Drupal\Core\Logger\LoggerChannelFactory;
use OpenEuropa\Twig\Loader\EuropaComponentLibraryLoader;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Load ECL components Twig templates.
 */
class ComponentLibraryLoader extends EuropaComponentLibraryLoader {

  use MessengerTrait;

  /**
   * Theme path, if any.
   *
   * @var string
   */
  protected $themePath;

  /**
   * Logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct($namespaces, $root, $theme, $directory, ThemeHandler $theme_handler, LoggerChannelFactory $logger_factory) {
    // Make sure the theme exists before getting its path.
    // This is necessary when the "oe_theme_helper" module is enabled before
    // the theme is or the theme is disabled and the "oe_theme_helper" is not.
    $path = '';
    if ($theme_handler->themeExists($theme)) {
      $this->themePath = $theme_handler->getTheme($theme)->getPath();
      $path = $this->themePath . DIRECTORY_SEPARATOR . $directory;
    }

    $this->logger = $logger_factory->get('ecl');
    parent::__construct($namespaces, $path, $root);
  }

}
