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
define('FLEXTYPE_VERSION', '0.9.7');

/**
 * Start the session
 */
Session::start();

/**
 * Init Registry
 */
$registry = new Registry();

/**
 * Load flextype settings
 *
 * 1. Set settings files paths.
 * 2. Load flextype default and flextype custom settings files.
 * 3. Merge settings.
 * 4. Store settings in the flextype registry.
 */
$default_flextype_settings_file_path = PATH['config']['default'] . '/settings.yaml';
$custom_flextype_settings_file_path  = PATH['config']['site'] . '/settings.yaml';

// Create config dir
! Filesystem::has(PATH['site'] . '/config/') and Filesystem::createDir(PATH['site'] . '/config/');

// Set settings if Flextype Default settings config files exist
if (! Filesystem::has($default_flextype_settings_file_path)) {
    throw new RuntimeException('Flextype Default settings config file does not exist.');
}

if (($default_flextype_settings_content = Filesystem::read($default_flextype_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $default_flextype_settings_file_path . ' - failed!');
} else {
    if (trim($default_flextype_settings_content) === '') {
        $default_flextype_settings = [];
    } else {
        $default_flextype_settings = Yaml::decode($default_flextype_settings_content);
    }
}

// Create flextype custom settings file
! Filesystem::has($custom_flextype_settings_file_path) and Filesystem::write($custom_flextype_settings_file_path, $default_flextype_settings_content);

if (($custom_flextype_settings_content = Filesystem::read($custom_flextype_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $custom_flextype_settings_file_path . ' - failed!');
} else {
    if (trim($custom_flextype_settings_content) === '') {
        $custom_flextype_settings = [];
    } else {
        $custom_flextype_settings = Yaml::decode($custom_flextype_settings_content);
    }
}

// Merge flextype settings
$flextype_settings = array_replace_recursive($default_flextype_settings, $custom_flextype_settings);

// Store flextype merged settings in the flextype registry.
$registry->set('flextype', $flextype_settings);

/**
 * Create new application
 */
$app = new App([
    'settings' => [
        'debug' => $registry->get('flextype.errors.display'),
        'whoops.editor' => $registry->get('flextype.whoops.editor'),
        'whoops.page_title' => $registry->get('flextype.whoops.page_title'),
        'displayErrorDetails' => $registry->get('flextype.display_error_details'),
        'addContentLengthHeader' => $registry->get('flextype.add_content_length_header'),
        'routerCacheFile' => $registry->get('flextype.router_cache_file'),
        'determineRouteBeforeAppMiddleware' => $registry->get('flextype.determine_route_before_app_middleware'),
        'outputBuffering' => $registry->get('flextype.output_buffering'),
        'responseChunkSize' => $registry->get('flextype.response_chunk_size'),
        'httpVersion' => $registry->get('flextype.http_version'),
        'twig' => [
            'charset' => $registry->get('flextype.twig.charset'),
            'debug' => $registry->get('flextype.twig.debug'),
            'cache' => $registry->get('flextype.twig.cache') ? PATH['cache'] . '/twig' : false,
            'auto_reload' => $registry->get('flextype.twig.auto_reload'),
        ],
        'images' => [
            'driver' => $registry->get('flextype.image.driver'),
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
 * Include API ENDPOINTS
 */
include_once 'api/delivery/images.php';
include_once 'api/delivery/entries.php';
include_once 'api/delivery/registry.php';

/**
 * Set internal encoding
 */
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding($flextype['registry']->get('flextype.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding($flextype['registry']->get('flextype.charset'));

/**
 * Display Errors
 */
if ($flextype['registry']->get('flextype.errors.display')) {

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
date_default_timezone_set($flextype['registry']->get('flextype.timezone'));
 
/**
 * Init shortocodes
 *
 * Load Flextype Shortcodes extensions from directory /flextype/shortcodes/ based on settings.shortcodes.extensions array
 */
$shortcodes_extensions = $flextype['registry']->get('flextype.shortcodes.extensions');

foreach ($shortcodes_extensions as $shortcodes_extension) {
    $shortcodes_extension_file_path = ROOT_DIR . '/flextype/shortcodes/' . $shortcodes_extension . 'ShortcodeExtension.php';
    if (file_exists($shortcodes_extension_file_path)) {
        include_once $shortcodes_extension_file_path;
    }
}

/**
 * Init plugins
 */
$flextype['plugins']->init($flextype, $app);

/**
 * Init themes
 */
$flextype['themes']->init($flextype, $app);

/**
 * Run application
 */
$app->run();
