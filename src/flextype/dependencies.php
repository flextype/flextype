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
use Thunder\Shortcode\ShortcodeFacade;
use function date;
use function extension_loaded;
use function ucfirst;

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
 * Add logger service to Flextype container
 */
$flextype['logger'] = static function ($container) {
    $logger = new Logger('flextype');
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
        'separator' => $container['registry']->get('flextype.settings.slugify.separator'),
        'lowercase' => $container['registry']->get('flextype.settings.slugify.lowercase'),
        'trim' => $container['registry']->get('flextype.settings.slugify.trim'),
        'regexp' => $container['registry']->get('flextype.settings.slugify.regexp'),
        'lowercase_after_regexp' => $container['registry']->get('flextype.settings.slugify.lowercase_after_regexp'),
        'strip_tags' => $container['registry']->get('flextype.settings.slugify.strip_tags'),
    ]);
};

/**
 * Adds the cache adapter to the Flextype container
 */
$flextype['cache_adapter'] = static function ($container) use ($flextype) {
    $driver_name = $container['registry']->get('flextype.settings.cache.driver');

    if (! $driver_name || $driver_name === 'auto') {
        if (extension_loaded('apcu')) {
            $driver_name = 'apcu';
        } elseif (extension_loaded('wincache')) {
            $driver_name = 'wincache';
        } else {
            $driver_name = 'filesystem';
        }
    }

    $class   = ucfirst($driver_name);
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
 * Add shortcodes service to Flextype container
 */
$flextype['shortcodes'] = static function ($container) {
    return new ShortcodeFacade();
};

/**
 * Add serializer service to Flextype container
 */
$flextype['serializer'] = static function ($container) use ($flextype) {
    return new Serializer($flextype);
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
        new Local(PATH['project'] . '/uploads/entries/')
    );

    // Set cache filesystem
    $cache = new Filesystem(
        new Local(PATH['cache'] . '/glide')
    );

    // Set watermarks filesystem
    $watermarks = new Filesystem(
        new Local(PATH['project'] . '/watermarks')
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
 * Add entries service to Flextype container
 */
$flextype['entries'] = static function ($container) {
    return new Entries($container);
};

/**
 * Add media folders service to Flextype container
 */
$flextype['media_folders'] = static function ($container) use ($flextype, $app) {
    return new MediaFolders($flextype, $app);
};

/**
 * Add media files service to Flextype container
 */
$flextype['media_files'] = static function ($container) use ($flextype, $app) {
    return new MediaFiles($flextype, $app);
};

/**
 * Add media folders meta service to Flextype container
 */
$flextype['media_folders_meta'] = static function ($container) use ($flextype, $app) {
    return new MediaFoldersMeta($flextype, $app);
};

/**
 * Add media files meta service to Flextype container
 */
$flextype['media_files_meta'] = static function ($container) use ($flextype, $app) {
    return new MediaFilesMeta($flextype, $app);
};

/**
 * Add plugins service to Flextype container
 */
$flextype['plugins'] = static function ($container) use ($flextype, $app) {
    return new Plugins($flextype, $app);
};

/**
 * Add cors service to Flextype container
 */
$flextype['cors'] = static function ($container) use ($flextype, $app) {
    return new Cors($flextype, $app);
};
