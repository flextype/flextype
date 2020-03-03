<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Bnf\Slim3Psr15\CallableResolver;
use Cocur\Slugify\Slugify;
use Intervention\Image\ImageManager;
use League\Event\Emitter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Glide\Api\Api;
use League\Glide\Manipulators\Background;
use League\Glide\Manipulators\Blur;
use League\Glide\Manipulators\Border;
use League\Glide\Manipulators\Brightness;
use League\Glide\Manipulators\Contrast;
use League\Glide\Manipulators\Crop;
use League\Glide\Manipulators\Encode;
use League\Glide\Manipulators\Filter;
use League\Glide\Manipulators\Gamma;
use League\Glide\Manipulators\Orientation;
use League\Glide\Manipulators\Pixelate;
use League\Glide\Manipulators\Sharpen;
use League\Glide\Manipulators\Size;
use League\Glide\Manipulators\Watermark;
use League\Glide\Responses\SlimResponseFactory;
use League\Glide\ServerFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Thunder\Shortcode\ShortcodeFacade;
use Twig\Extension\DebugExtension;
use function date;
use function ucfirst;
use function extension_loaded;

/**
 * Supply a custom callable resolver, which resolves PSR-15 middlewares.
 */
$flextype['callableResolver'] = static function ($container) {
    return new CallableResolver($container);
};

/**
 * Add registry service to Flextype container
 */
$flextype['registry'] = static function ($container) use ($registry) {
    return $registry;
};

/**
 * Add CSRF (cross-site request forgery) protection service to Flextype container
 */
$flextype['csrf'] = static function ($container) {
    return new Guard();
};

/**
 * Add logger service to Flextype container
 */
$flextype['logger'] = static function ($container) {
    $logger       = new Logger('flextype');
    $logger->pushHandler(new StreamHandler(PATH['logs'] . '/' . date('Y-m-d') . '.log'));
    return $logger;
};

/**
 * Add emitter service to Flextype container
 */
$flextype['emitter'] = static function ($container) {
    return new Emitter();
};

/**
 * Add slugify service to Flextype container
 */
$flextype['slugify'] = static function ($container) {
    return new Slugify([
        'separator' => $container['registry']->get('flextype.slugify.separator'),
        'lowercase' => $container['registry']->get('flextype.slugify.lowercase'),
        'trim' => $container['registry']->get('flextype.slugify.trim'),
        'regexp' => $container['registry']->get('flextype.slugify.regexp'),
        'lowercase_after_regexp' => $container['registry']->get('flextype.slugify.lowercase_after_regexp'),
        'strip_tags' => $container['registry']->get('flextype.slugify.strip_tags'),
    ]);
};

/**
 * Add flash service to Flextype container
 */
$flextype['flash'] = static function ($container) {
    return new Messages();
};

/**
 * Adds the cache adapter to the Flextype container
 */
$flextype['cache_adapter'] = static function ($container) use ($flextype) {
    $driver_name = $container['registry']->get('flextype.cache.driver');

    if (! $driver_name || $driver_name === 'auto') {
        if (extension_loaded('apcu')) {
            $driver_name = 'apcu';
        } elseif (extension_loaded('wincache')) {
            $driver_name = 'wincache';
        } else {
            $driver_name = 'filesystem';
        }
    }

    $class = ucfirst($driver_name);
    $adapter = "Flextype\\Cache\\{$class}Adapter";

    return new $adapter($flextype);
};

/**
 * Add cache service to Flextype container
 */
$flextype['cache'] = static function ($container) use ($flextype) {
    return new Cache($flextype);
};

/**
 * Add parser service to Flextype container
 */
$flextype['parser'] = static function ($container) use ($flextype) {
    return new Parser($flextype);
};

/**
 * Add images service to Flextype container
 */
$flextype['images'] = static function ($container) {
    // Get images settings
    $imagesSettings = $container->get('settings')['images'];

    // Set source filesystem
    $source = new Filesystem(
        new Local(PATH['uploads'] . '/entries/')
    );

    // Set cache filesystem
    $cache = new Filesystem(
        new Local(PATH['cache'] . '/glide')
    );

    // Set watermarks filesystem
    $watermarks = new Filesystem(
        new Local(PATH['site'] . '/watermarks')
    );

    // Set image manager
    $imageManager = new ImageManager($imagesSettings);

    // Set manipulators
    $manipulators = [
        new Orientation(),
        new Crop(),
        new Size(2000*2000),
        new Brightness(),
        new Contrast(),
        new Gamma(),
        new Sharpen(),
        new Filter(),
        new Blur(),
        new Pixelate(),
        new Watermark($watermarks),
        new Background(),
        new Border(),
        new Encode(),
    ];

    // Set API
    $api = new Api($imageManager, $manipulators);

    // Setup Glide server
    return ServerFactory::create([
        'source' => $source,
        'cache' => $cache,
        'api' => $api,
        'response' => new SlimResponseFactory(),
    ]);
};

/**
 * Add fieldsets service to Flextype container
 */
$flextype['fieldsets'] = static function ($container) use ($flextype) {
    return new Fieldsets($flextype);
};

/**
 * Add snippets service to Flextype container
 */
$flextype['snippets'] = static function ($container) use ($flextype, $app) {
    return new Snippets($flextype, $app);
};

/**
 * Add shortcodes service to Flextype container
 */
$flextype['shortcodes'] = static function ($container) {
    return new ShortcodeFacade();
};


/**
 * Add entries service to Flextype container
 */
$flextype['entries'] = static function ($container) {
    return new Entries($container);
};

/**
 * Add view service to Flextype container
 */
$flextype['view'] = static function ($container) {
    // Get twig settings
    $twigSettings = $container->get('settings')['twig'];

    // Create Twig View
    $view = new Twig(PATH['site'], $twigSettings);

    // Instantiate
    $router = $container->get('router');
    $uri    = Uri::createFromEnvironment(new Environment($_SERVER));

    // Add Twig Extension
    $view->addExtension(new TwigExtension($router, $uri));

    // Add Twig Debug Extension
    $view->addExtension(new DebugExtension());

    // Load Flextype Twig extensions from directory /flextype/twig/ based on settings.twig.extensions array
    $twig_extensions = $container['registry']->get('flextype.twig.extensions');

    foreach ($twig_extensions as $twig_extension) {
        $twig_extension_class_name = $twig_extension . 'TwigExtension';
        $twig_extension_class_name_with_namespace = 'Flextype\\' . $twig_extension . 'TwigExtension';

        if (file_exists(ROOT_DIR . '/flextype/twig/' . $twig_extension_class_name . '.php')) {
            $view->addExtension(new $twig_extension_class_name_with_namespace($container));
        }
    }

    // Return view
    return $view;
};

/**
 * Add themes service to Flextype container
 */
$flextype['themes'] = static function ($container) use ($flextype, $app) {
    return new Themes($flextype, $app);
};

/**
 * Add plugins service to Flextype container
 */
$flextype['plugins'] = static function ($container) use ($flextype, $app) {
    return new Plugins($flextype, $app);
};
