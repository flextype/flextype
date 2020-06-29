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
use function trim;

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
 * 2. Load flextype default and flextype custom project settings files.
 * 3. Merge settings.
 * 4. Store settings in the flextype registry.
 */
$flextype_manifest_file_path         = ROOT_DIR . '/src/flextype/flextype.yaml';
$default_flextype_settings_file_path = ROOT_DIR . '/src/flextype/settings.yaml';
$custom_flextype_settings_file_path  = PATH['project'] . '/config/flextype/settings.yaml';

// Create config dir
! Filesystem::has(PATH['project'] . '/config/flextype/') and Filesystem::createDir(PATH['project'] . '/config/flextype/');

// Set settings if Flextype Default settings config files exist
if (! Filesystem::has($default_flextype_settings_file_path)) {
    throw new RuntimeException('Flextype Default settings config file does not exist.');
}

if (($default_flextype_settings_content = Filesystem::read($default_flextype_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $default_flextype_settings_file_path . ' - failed!');
} else {
    if (trim($default_flextype_settings_content) === '') {
        $default_flextype_settings['settings'] = [];
    } else {
        $default_flextype_settings['settings'] = Yaml::decode($default_flextype_settings_content);
    }
}

// Create flextype custom settings file
! Filesystem::has($custom_flextype_settings_file_path) and Filesystem::write($custom_flextype_settings_file_path, $default_flextype_settings_content);

if (($custom_flextype_settings_content = Filesystem::read($custom_flextype_settings_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $custom_flextype_settings_file_path . ' - failed!');
} else {
    if (trim($custom_flextype_settings_content) === '') {
        $custom_flextype_settings['settings'] = [];
    } else {
        $custom_flextype_settings['settings'] = Yaml::decode($custom_flextype_settings_content);
    }
}

if (($flextype_manifest_content = Filesystem::read($flextype_manifest_file_path)) === false) {
    throw new RuntimeException('Load file: ' . $flextype_manifest_file_path . ' - failed!');
} else {
    if (trim($flextype_manifest_content) === '') {
        $flextype_manifest['manifest'] = [];
    } else {
        $flextype_manifest['manifest'] = Yaml::decode($flextype_manifest_content);
    }
}

// Merge flextype default settings with custom project settings.
$flextype_settings = array_replace_recursive($default_flextype_settings, $custom_flextype_settings, $flextype_manifest);

// Store flextype merged settings in the flextype registry.
$registry->set('flextype', $flextype_settings);

/**
 * Create new application
 */
$app = new App([
    'settings' => [
        'debug' => $registry->get('flextype.settings.errors.display'),
        'whoops.editor' => $registry->get('flextype.settings.whoops.editor'),
        'whoops.page_title' => $registry->get('flextype.settings.whoops.page_title'),
        'displayErrorDetails' => $registry->get('flextype.settings.display_error_details'),
        'addContentLengthHeader' => $registry->get('flextype.settings.add_content_length_header'),
        'routerCacheFile' => $registry->get('flextype.settings.router_cache_file'),
        'determineRouteBeforeAppMiddleware' => $registry->get('flextype.settings.determine_route_before_app_middleware'),
        'outputBuffering' => $registry->get('flextype.settings.output_buffering'),
        'responseChunkSize' => $registry->get('flextype.settings.response_chunk_size'),
        'httpVersion' => $registry->get('flextype.settings.http_version'),
        'images' => [
            'driver' => $registry->get('flextype.settings.image.driver'),
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
 * Include API ENDPOINTS
 */
include_once 'endpoints/access.php';
include_once 'endpoints/entries.php';
include_once 'endpoints/registry.php';
include_once 'endpoints/config.php';
include_once 'endpoints/files.php';
include_once 'endpoints/folders.php';
include_once 'endpoints/images.php';

/**
 * Set internal encoding
 */
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding($flextype['registry']->get('flextype.settings.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding($flextype['registry']->get('flextype.settings.charset'));

/**
 * Display Errors
 */
if ($flextype['registry']->get('flextype.settings.errors.display')) {

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
date_default_timezone_set($flextype['registry']->get('flextype.settings.timezone'));

/**
 * Init shortocodes
 *
 * Load Flextype Shortcodes extensions from directory /flextype/shortcodes/ based on settings.shortcodes.extensions array
 */
$shortcodes_extensions = $flextype['registry']->get('flextype.settings.shortcodes.extensions');

foreach ($shortcodes_extensions as $shortcodes_extension) {
    $shortcodes_extension_file_path = ROOT_DIR . '/src/flextype/core/Parsers/shortcodes/' . $shortcodes_extension . 'ShortcodeExtension.php';
    if (file_exists($shortcodes_extension_file_path)) {
        include_once $shortcodes_extension_file_path;
    }
}

/**
 * Init plugins
 */
$flextype['plugins']->init($flextype, $app);

/**
 * Enable lazy CORS
 *
 * CORS (Cross-origin resource sharing) allows JavaScript web apps to make HTTP requests to other domains.
 * This is important for third party web apps using Flextype, as without CORS, a JavaScript app hosted on example.com
 * couldn't access our APIs because they're hosted on another.com which is a different domain.
 */
$flextype['cors']->init();

/**
 * Run application
 */
$app->run();
