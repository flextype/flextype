<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Session\Session;
use RuntimeException;
use Slim\App;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;
use function array_replace_recursive;
use function date_default_timezone_set;
use function define;
use function error_reporting;
use function function_exists;
use function mb_internal_encoding;
use function mb_language;
use function mb_regex_encoding;

/**
 * The version of Flextype
 *
 * @var string
 */
define('FLEXTYPE_VERSION', '0.9.5');

/**
 * Start the session
 */
Session::start();

/**
 * Init Registry
 */
$registry = new Registry();

/**
 * Load core settings
 *
 * 1. Set settings files paths.
 * 2. Load system default and site settings files.
 * 3. Merge settings.
 * 4. Add settings into the registry.
 */
$default_settings_file_path = PATH['config']['default'] . '/settings.yaml';
$site_settings_file_path    = PATH['config']['site'] . '/settings.yaml';

// Set settings if Flextype settings and Site settings config files exist
if (! Filesystem::has($default_settings_file_path) || ! Filesystem::has($site_settings_file_path)) {
    throw new RuntimeException('Flextype settings and Site settings config files does not exist.');
}

if (($content = Filesystem::read($default_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $default_settings_file_path . ' - failed!');
} else {
    $default_settings = Parser::decode($content, 'yaml');
}

if (($content = Filesystem::read($site_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $site_settings_file_path . ' - failed!');
} else {
    $site_settings = Parser::decode($content, 'yaml');
}

// Merge settings
$settings = array_replace_recursive($default_settings, $site_settings);

// Set settings
$registry->set('settings', $settings);

/**
 * Create new application
 */
$app = new App([
    'settings' => [
        'debug' => $registry->get('settings.errors.display'),
        'whoops.editor' => $registry->get('settings.whoops.editor'),
        'whoops.page_title' => $registry->get('settings.whoops.page_title'),
        'displayErrorDetails' => $registry->get('settings.display_error_details'),
        'addContentLengthHeader' => $registry->get('settings.add_content_length_header'),
        'routerCacheFile' => $registry->get('settings.router_cache_file'),
        'determineRouteBeforeAppMiddleware' => $registry->get('settings.determine_route_before_app_middleware'),
        'outputBuffering' => $registry->get('settings.output_buffering'),
        'responseChunkSize' => $registry->get('settings.response_chunk_size'),
        'httpVersion' => $registry->get('settings.http_version'),
        'twig' => [
            'charset' => $registry->get('settings.twig.charset'),
            'debug' => $registry->get('settings.twig.debug'),
            'cache' => $registry->get('settings.twig.cache') ? PATH['cache'] . '/twig' : false,
            'auto_reload' => $registry->get('settings.twig.auto_reload'),
        ],
        'images' => [
            'driver' => $registry->get('settings.image.driver'),
        ],
    ],
]);

/**
 * Set Flextype Dependency Injection Container
 */
$flextype = $app->getContainer();

/**
 * Include Dependencies
 */
include_once 'dependencies.php';

/**
 * Include Middlewares
 */
include_once 'middlewares.php';

/**
 * Include Routes (web)
 */
include_once 'routes/web.php';


/**
 * Set internal encoding
 */
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding($flextype['registry']->get('settings.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding($flextype['registry']->get('settings.charset'));

/**
 * Display Errors
 */
if ($flextype['registry']->get('settings.errors.display')) {

    /**
     * Add WhoopsMiddleware
     */
    $app->add(new WhoopsMiddleware($app));
} else {
    error_reporting(0);
}

/**
 * Set default timezone
 */
date_default_timezone_set($flextype['registry']->get('settings.timezone'));

/**
 * Get and Include default shortcodes
 */
foreach (Filesystem::listContents(ROOT_DIR . '/flextype/shortcodes') as $shortcode) {
    include_once $shortcode['path'];
}

/**
 * Init themes
 */
$flextype['themes']->init($flextype, $app);

/**
 * Init plugins
 */
$flextype['plugins']->init($flextype, $app);

/**
 * Run application
 */
$app->run();
