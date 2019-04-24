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
use Flextype\Component\ErrorHandler\ErrorHandler;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Filesystem\Filesystem;
use Thunder\Shortcode\ShortcodeFacade;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Flash\Messages;
use League\Glide\ServerFactory;
use League\Glide\Responses\SlimResponseFactory;
use League\Event\Emitter;

/**
 * The version of Flextype
 *
 * @var string
 */
define ('FLEXTYPE_VERSION', '0.8.3');

// Start the session
Session::start();

// Configure application
$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => true,
        'addContentLengthHeader' => false,
        'routerCacheFile' => false,
        'determineRouteBeforeAppMiddleware' => false,
        'outputBuffering' => 'append',
        'responseChunkSize' => 4096,
        'httpVersion' => '1.1',

        'twig' => [
            'cache' => false
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
 * Set Flextype Dependency Container
 */
$flextype = $app->getContainer();

/**
 * Add CSRF (cross-site request forgery) protection service to Flextype container
 */
$flextype['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

/**
 * Add middleware CSRF (cross-site request forgery) protection for all routes
 */
$app->add($flextype->get('csrf'));

/**
 * Add emitter service to Flextype container
 */
$flextype['emitter'] = function($container) {
    return new Emitter();
};

/**
 * Add flash service to Flextype container
 */
$flextype['flash'] = function ($container) {
    return new Messages();
};

/**
 * Add registry service to Flextype container
 */
$flextype['registry'] = function($container) {
    return new Registry();
};

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

/**
 * Set error handler
 *
 * @access private
 */

// Display Errors
if ($flextype['registry']->get('settings.errors.display')) {
    //define('DEVELOPMENT', true);
    error_reporting(-1);
} else {
    //define('DEVELOPMENT', false);
    error_reporting(0);
}

// Create directory for logs
!Filesystem::has(LOGS_PATH) and Filesystem::createDir(LOGS_PATH);

// Set Error handler
//set_error_handler('Flextype\Component\ErrorHandler\ErrorHandler::error');
//register_shutdown_function('Flextype\Component\ErrorHandler\ErrorHandler::fatal');
//set_exception_handler('Flextype\Component\ErrorHandler\ErrorHandler::exception');


// Set default timezone
date_default_timezone_set($flextype['registry']->get('settings.timezone'));

/**
 * Add cache service to Flextype container
 */
$flextype['cache'] = function($container) use ($flextype) {
    return new Cache($flextype);
};

/**
 * Add images service to Flextype container
 */
$flextype['images'] = function($container) {

    // Get images settings
    $imagesSettings = $container->get('settings')['images'];

    // Set source filesystem
    $source = new \League\Flysystem\Filesystem(
        new \League\Flysystem\Adapter\Local(PATH['entries'])
    );

    // Set cache filesystem
    $cache = new \League\Flysystem\Filesystem(
        new \League\Flysystem\Adapter\Local(PATH['cache'] . '/glide')
    );

    // Set watermarks filesystem
    $watermarks = new \League\Flysystem\Filesystem(
        new \League\Flysystem\Adapter\Local(PATH['site'] . '/watermarks')
    );

    // Set image manager
    $imageManager = new \Intervention\Image\ImageManager($imagesSettings);

    // Set manipulators
    $manipulators = [
        new \League\Glide\Manipulators\Orientation(),
        new \League\Glide\Manipulators\Crop(),
        new \League\Glide\Manipulators\Size(2000*2000),
        new \League\Glide\Manipulators\Brightness(),
        new \League\Glide\Manipulators\Contrast(),
        new \League\Glide\Manipulators\Gamma(),
        new \League\Glide\Manipulators\Sharpen(),
        new \League\Glide\Manipulators\Filter(),
        new \League\Glide\Manipulators\Blur(),
        new \League\Glide\Manipulators\Pixelate(),
        new \League\Glide\Manipulators\Watermark($watermarks),
        new \League\Glide\Manipulators\Background(),
        new \League\Glide\Manipulators\Border(),
        new \League\Glide\Manipulators\Encode(),
    ];

    // Set API
    $api = new \League\Glide\Api\Api($imageManager, $manipulators);

    // Setup Glide server
    $server = \League\Glide\ServerFactory::create([
        'source' => $source,
        'cache' => $cache,
        'api' => $api,
        'response' => new SlimResponseFactory(),
    ]);

    return $server;
};

/**
 * Add fieldsets service to Flextype container
 */
$flextype['fieldsets'] = function($container) use ($flextype) {
    return new Fieldsets($flextype);
};

/**
 * Add snippets service to Flextype container
 */
$flextype['snippets'] = function($container) use ($flextype){
    return new Snippets($flextype);
};

/**
 * Add shortcodes service to Flextype container
 */
$flextype['shortcodes'] = function($container) {
    return new ShortcodeFacade();
};

// Get Default Shortocdes List
$shortcodes_list = Filesystem::listContents(ROOT_DIR . '/flextype/shortcodes');

// Include default shortcodes
foreach ($shortcodes_list as $shortcode) {
    include_once $shortcode['path'];
}

/**
 * Add entries service to Flextype container
 */
$flextype['entries'] = function($container) {
    return new Entries($container);
};

/**
 * Add view service to Flextype container
 */
$flextype['view'] = function ($container) {

    // Get twig settings
    $twigSettings = $container->get('settings')['twig'];

    // Create Twig View
    $view = new \Slim\Views\Twig(PATH['site'], $twigSettings);

    // Instantiate
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));

    // Add Twig Extension
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // Add Entries Twig Extension
    $view->addExtension(new EntriesTwigExtension($container));

    // Add Emitter Twig Extension
    $view->addExtension(new EmitterTwigExtension($container));

    // Add Flash Twig Extension
    $view->addExtension(new FlashTwigExtension($container));

    // Add I18n Twig Extension
    $view->addExtension(new I18nTwigExtension());

    // Add Assets Twig Extension
    $view->addExtension(new AssetsTwigExtension());

    // Add Csrf Twig Extension
    $view->addExtension(new CsrfTwigExtension($container->get('csrf')));

    // Add Global Vars Twig Extension
    $view->addExtension(new GlobalVarsTwigExtension($container));

    // Return view
    return $view;
};

/**
 * Generates and returns the image response
 */
$app->get('/image/{path:.+}', function (Request $request, Response $response, array $args) use ($flextype) {
    return $flextype['images']->getImageResponse($args['path'], $_GET);
});

/**
 * Add plugins service to Flextype container
 */
$flextype['plugins'] = function($container) use ($flextype, $app) {
    return new Plugins($flextype, $app);
};

/**
 * Init plugins
 */
$flextype['plugins']->init($flextype, $app);

/**
 * Run application
 */
$app->run();
