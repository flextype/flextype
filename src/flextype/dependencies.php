<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Bnf\Slim3Psr15\CallableResolver;
use Cocur\Slugify\Slugify;
use Flextype\App\Foundation\Cache\Cache;
use Flextype\App\Foundation\Cors;
use Flextype\App\Foundation\Entries\Entries;
use Flextype\App\Foundation\Media\MediaFiles;
use Flextype\App\Foundation\Media\MediaFilesMeta;
use Flextype\App\Foundation\Media\MediaFolders;
use Flextype\App\Foundation\Media\MediaFoldersMeta;
use Flextype\App\Foundation\Plugins;
use Flextype\App\Support\Parsers\Markdown;
use Flextype\App\Support\Parsers\Shortcode;
use Flextype\App\Support\Serializers\Frontmatter;
use Flextype\App\Support\Serializers\Json;
use Flextype\App\Support\Serializers\Yaml;
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
use ParsedownExtra;
use Thunder\Shortcode\ShortcodeFacade;
use function date;
use function extension_loaded;


/**
 * Supply a custom callable resolver, which resolves PSR-15 middlewares.
 */
flextype()->container()['callableResolver'] = static function () {
    return new CallableResolver(flextype()->container());
};

/**
 * Add registry service to Flextype container
 */
flextype()->container()['registry'] = static function () use ($registry) {
    return $registry;
};

/**
 * Add logger service to Flextype container
 */
flextype()->container()['logger'] = static function () {
    $logger = new Logger('flextype');
    $logger->pushHandler(new StreamHandler(PATH['logs'] . '/' . date('Y-m-d') . '.log'));

    return $logger;
};

/**
 * Add emitter service to Flextype container
 */
flextype()->container()['emitter'] = static function () {
    return new Emitter();
};

/**
 * Add slugify service to Flextype container
 */
flextype()->container()['slugify'] = static function () {
    return new Slugify([
        'separator' => flextype('registry')->get('flextype.settings.slugify.separator'),
        'lowercase' => flextype('registry')->get('flextype.settings.slugify.lowercase'),
        'trim' => flextype('registry')->get('flextype.settings.slugify.trim'),
        'regexp' => flextype('registry')->get('flextype.settings.slugify.regexp'),
        'lowercase_after_regexp' => flextype('registry')->get('flextype.settings.slugify.lowercase_after_regexp'),
        'strip_tags' => flextype('registry')->get('flextype.settings.slugify.strip_tags'),
    ]);
};

/**
 * Adds the cache adapter to the Flextype container
 */
flextype()->container()['cache_adapter'] = static function () {
    $driver_name = flextype('registry')->get('flextype.settings.cache.driver');

    if (! $driver_name || $driver_name === 'auto') {
        if (extension_loaded('apcu')) {
            $driver_name = 'apcu';
        } elseif (extension_loaded('wincache')) {
            $driver_name = 'wincache';
        } else {
            $driver_name = 'phparrayfile';
        }
    }

    $drivers_classes = [
        'apcu' => 'Apcu',
        'wincache' => 'WinCache',
        'phpfile' => 'PhpFile',
        'phparrayfile' => 'PhpArrayFile',
        'array' => 'Array',
        'filesystem' => 'Filesystem',
        'memcached' => 'Memcached',
        'redis' => 'Redis',
        'sqlite3' => 'SQLite3',
        'zenddatacache' => 'ZendDataCache',
    ];

    $class_name = $drivers_classes[$driver_name];

    $adapter = "Flextype\\App\\Foundation\\Cache\\{$class_name}CacheAdapter";

    return new $adapter();
};

/**
 * Add cache service to Flextype container
 */
flextype()->container()['cache'] = static function () {
    return new Cache();
};

/**
 * Add shortcode parser service to Flextype container
 */
flextype()->container()['shortcode'] = static function () {
    return new Shortcode(new ShortcodeFacade());
};

/**
 * Add markdown parser service to Flextype container
 */
flextype()->container()['markdown'] = static function () {
    return new Markdown(new ParsedownExtra());
};

/**
 * Add json serializer service to Flextype container
 */
flextype()->container()['json'] = static function () {
    return new Json();
};

/**
 * Add yaml serializer service to Flextype container
 */
flextype()->container()['yaml'] = static function () {
    return new Yaml();
};

/**
 * Add frontmatter serializer service to Flextype container
 */
flextype()->container()['frontmatter'] = static function () {
    return new Frontmatter();
};

/**
 * Add images service to Flextype container
 */
flextype()->container()['images'] = static function () {
    // Get images settings
    $imagesSettings = ['driver' => flextype('registry')->get('flextype.settings.image.driver')];

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
flextype()->container()['entries'] = static function () {
    return new Entries();
};

/**
 * Add media folders service to Flextype container
 */
flextype()->container()['media_folders'] = static function () {
    return new MediaFolders();
};

/**
 * Add media files service to Flextype container
 */
flextype()->container()['media_files'] = static function () {
    return new MediaFiles();
};

/**
 * Add media folders meta service to Flextype container
 */
flextype()->container()['media_folders_meta'] = static function () {
    return new MediaFoldersMeta();
};

/**
 * Add media files meta service to Flextype container
 */
flextype()->container()['media_files_meta'] = static function () {
    return new MediaFilesMeta();
};

/**
 * Add plugins service to Flextype container
 */
flextype()->container()['plugins'] = static function () {
    return new Plugins();
};

/**
 * Add cors service to Flextype container
 */
flextype()->container()['cors'] = static function () {
    return new Cors();
};
