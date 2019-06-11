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

use Flextype\Component\Registry\Registry;
use Thunder\Shortcode\ShortcodeFacade;
use Slim\Flash\Messages;
use Cocur\Slugify\Slugify;
use League\Glide\ServerFactory;
use League\Glide\Responses\SlimResponseFactory;
use League\Event\Emitter;

/**
 * Add CSRF (cross-site request forgery) protection service to Flextype container
 */
$flextype['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

/**
 * Add logger
 */
$flextype['logger'] = function($container) {
    $logger = new \Monolog\Logger('flextype');
    $file_handler = new \Monolog\Handler\StreamHandler(PATH['site'] . '/logs/' . date('Y-m-d') . '.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

/**
 * Add emitter service to Flextype container
 */
$flextype['emitter'] = function ($container) {
    return new Emitter();
};

/**
 * Add slugify service to Flextype container
 */
$flextype['slugify'] = function ($container) {
    return new Slugify(['separator' => '-', 'lowercase' => true, 'trim' => true]);
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
$flextype['registry'] = function ($container) {
    return new Registry();
};

/**
 * Add cache service to Flextype container
 */
$flextype['cache'] = function ($container) use ($flextype) {
    return new Cache($flextype);
};

/**
 * Add images service to Flextype container
 */
$flextype['images'] = function ($container) {

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
$flextype['fieldsets'] = function ($container) use ($flextype) {
    return new Fieldsets($flextype);
};

/**
 * Add snippets service to Flextype container
 */
$flextype['snippets'] = function ($container) use ($flextype) {
    return new Snippets($flextype);
};

/**
 * Add shortcodes service to Flextype container
 */
$flextype['shortcodes'] = function ($container) {
    return new ShortcodeFacade();
};

/**
 * Add entries service to Flextype container
 */
$flextype['entries'] = function ($container) {
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

    // Add Twig Debug Extension
    $view->addExtension(new \Twig\Extension\DebugExtension());

    // Add Entries Twig Extension
    $view->addExtension(new EntriesTwigExtension($container));

    // Add Emitter Twig Extension
    $view->addExtension(new EmitterTwigExtension($container));

    // Add Flash Twig Extension
    $view->addExtension(new FlashTwigExtension($container));

    // Add I18n Twig Extension
    $view->addExtension(new I18nTwigExtension());

    // Add JsonParser Extension
    $view->addExtension(new JsonParserTwigExtension());

    // Add Filesystem Extension
    $view->addExtension(new FilesystemTwigExtension());

    // Add Assets Twig Extension
    $view->addExtension(new AssetsTwigExtension());

    // Add Csrf Twig Extension
    $view->addExtension(new CsrfTwigExtension($container->get('csrf')));

    // Add Global Vars Twig Extension
    $view->addExtension(new GlobalVarsTwigExtension($container));

    // Add Global Shortcodes Twig Extension
    $view->addExtension(new ShortcodesTwigExtension($container));

    // Return view
    return $view;
};

/**
 * Add themes service to Flextype container
 */
$flextype['themes'] = function ($container) use ($flextype, $app) {
     return new Themes($flextype, $app);
};

/**
 * Add plugins service to Flextype container
 */
$flextype['plugins'] = function ($container) use ($flextype, $app) {
     return new Plugins($flextype, $app);
};
