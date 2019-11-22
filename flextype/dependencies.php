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
 * Add logger
 */
$flextype['logger'] = static function ($container) {
    $logger       = new Logger('flextype');
    $file_handler = new StreamHandler(PATH['site'] . '/logs/' . date('Y-m-d') . '.log');
    $logger->pushHandler($file_handler);

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
        'separator' => $container['registry']->get('settings.slugify.separator'),
        'lowercase' => $container['registry']->get('settings.slugify.lowercase'),
        'trim' => $container['registry']->get('settings.slugify.trim'),
        'regexp' => $container['registry']->get('settings.slugify.regexp'),
        'lowercase_after_regexp' => $container['registry']->get('settings.slugify.lowercase_after_regexp'),
        'strip_tags' => $container['registry']->get('settings.slugify.strip_tags'),
    ]);
};

/**
 * Add flash service to Flextype container
 */
$flextype['flash'] = static function ($container) {
    return new Messages();
};

/**
 * Add cache service to Flextype container
 */
$flextype['cache'] = static function ($container) use ($flextype) {
    return new Cache($flextype);
};

/**
 * Add cache service to Flextype container
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
        new Local(PATH['entries'])
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
 * Add forms service to Flextype container
 */
$flextype['forms'] = static function ($container) use ($flextype) {
    return new Forms($flextype);
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

    // Add Cache Twig Extension
    $view->addExtension(new CacheTwigExtension($container));

    // Add Entries Twig Extension
    $view->addExtension(new EntriesTwigExtension($container));

    // Add Emitter Twig Extension
    $view->addExtension(new EmitterTwigExtension($container));

    // Add Flash Twig Extension
    $view->addExtension(new FlashTwigExtension($container));

    // Add I18n Twig Extension
    $view->addExtension(new I18nTwigExtension());

    // Add Json Twig Extension
    $view->addExtension(new JsonTwigExtension($container));

    // Add Yaml Twig Extension
    $view->addExtension(new YamlTwigExtension($container));

    // Add Parser Twig Extension
    $view->addExtension(new ParserTwigExtension($container));

    // Add Markdown Twig Extension
    $view->addExtension(new MarkdownTwigExtension($container));

    // Add Filesystem Twig Extension
    $view->addExtension(new FilesystemTwigExtension());

    // Add Date Twig Extension
    $view->addExtension(new DateTwigExtension());

    // Add Assets Twig Extension
    $view->addExtension(new AssetsTwigExtension());

    // Add Csrf Twig Extension
    $view->addExtension(new CsrfTwigExtension($container->get('csrf')));

    // Add Global Vars Twig Extension
    $view->addExtension(new GlobalVarsTwigExtension($container));

    // Add Global Shortcodes Twig Extension
    $view->addExtension(new ShortcodesTwigExtension($container));

    // Add Global Snippets Twig Extension
    $view->addExtension(new SnippetsTwigExtension($container));

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
