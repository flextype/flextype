<?php

/**
 * @package Flextype
 *
 * @author Romanenko Sergey <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Filesystem\Filesystem;

/**
 * The version of Flextype
 *
 * @var string
 */
define('FLEXTYPE_VERSION', '0.9.0-beta');

// Start the session
Session::start();

// Configure application
$config = [
    'settings' => [
        'debug' => true,
        'whoops.editor' => 'atom',
        'whoops.page_title' => 'Error!',
        'displayErrorDetails' => true,
        'addContentLengthHeader' => true,
        'addContentLengthHeader' => false,
        'routerCacheFile' => false,
        'determineRouteBeforeAppMiddleware' => false,
        'outputBuffering' => 'append',
        'responseChunkSize' => 4096,
        'httpVersion' => '1.1',
        'twig' => [
            'cache' => PATH['site'] . '/cache/twig',
            'auto_reload' => true
        ],
        'images' => [
            'driver' => 'gd',
        ],
    ],
];

/**
 * Create new application
 */
$app = new \Slim\App($config);

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
 * Include Routes
 */
include_once 'routes/web.php';


// Set empty settings array
$flextype['registry']->set('settings', []);

// Set settings files path
$default_settings_file_path = PATH['config']['default'] . '/settings.json';
$site_settings_file_path    = PATH['config']['site'] . '/settings.json';

// Set settings if Flextype settings and Site settings config files exist
if (Filesystem::has($default_settings_file_path) && Filesystem::has($site_settings_file_path)) {
    if (($content = Filesystem::read($default_settings_file_path)) === false) {
        throw new \RuntimeException('Load file: ' . $default_settings_file_path . ' - failed!');
    } else {
        $default_settings = JsonParser::decode($content);
    }

    if (($content = Filesystem::read($site_settings_file_path)) === false) {
        throw new \RuntimeException('Load file: ' . $site_settings_file_path . ' - failed!');
    } else {
        $site_settings = JsonParser::decode($content);
    }

    // Merge settings
    $settings = array_replace_recursive($default_settings, $site_settings);

    // Set settings
    $flextype['registry']->set('settings', $settings);
} else {
    throw new \RuntimeException("Flextype settings and Site settings config files does not exist.");
}

// Set internal encoding
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding($flextype['registry']->get('settings.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding($flextype['registry']->get('settings.charset'));

// Display Errors
if ($flextype['registry']->get('settings.errors.display')) {

    /**
     * Add WhoopsMiddleware
     */
    $app->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware($app));

} else {
    error_reporting(0);
}

// Set default timezone
date_default_timezone_set($flextype['registry']->get('settings.timezone'));

// Get Default Shortocdes List
$shortcodes_list = Filesystem::listContents(ROOT_DIR . '/flextype/shortcodes');

// Include default shortcodes
foreach ($shortcodes_list as $shortcode) {
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
