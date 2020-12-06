<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Atomastic\Session\Session;
use Bnf\Slim3Psr15\CallableResolver;
use Cocur\Slugify\Slugify;
use Flextype\Foundation\Cors;
use Flextype\Foundation\Entries\Entries;
use Flextype\Foundation\Media\MediaFiles;
use Flextype\Foundation\Media\MediaFilesMeta;
use Flextype\Foundation\Media\MediaFolders;
use Flextype\Foundation\Media\MediaFoldersMeta;
use Flextype\Foundation\Plugins;
use Flextype\Support\Parsers\Markdown;
use Flextype\Support\Parsers\Shortcode;
use Flextype\Support\Serializers\Frontmatter;
use Flextype\Support\Serializers\Json;
use Flextype\Support\Serializers\Yaml;
use Intervention\Image\ImageManager;
use League\Event\Emitter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Flysystem;
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
use Phpfastcache\Drivers\Apcu\Config;
use Phpfastcache\Helper\Psr16Adapter as Cache;
use Thunder\Shortcode\ShortcodeFacade;

use function date;
use function extension_loaded;
use function flextype;
use function in_array;
use function strings;
use function sys_get_temp_dir;

/**
 * Create a standard session hanndler
 */
flextype()->container()['session'] = static function () {
    return new Session();
};

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
    $logger->pushHandler(new StreamHandler(PATH['tmp'] . '/logs/' . date('Y-m-d') . '.log'));

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


flextype()->container()['cache'] = static function () {
    $driverName = flextype('registry')->get('flextype.settings.cache.driver');

    $config = [];

    function getDriverConfig(string $driverName): array
    {
        $config = [];

        foreach (flextype('registry')->get('flextype.settings.cache.drivers.' . $driverName) as $key => $value) {
            if ($key === 'path' && in_array($driverName, ['files', 'sqlite', 'leveldb'])) {
                $config['path'] = ! empty($value) ? PATH['tmp'] . '/' . $value : sys_get_temp_dir();
            } else {
                $config[strings($key)->camel()->toString()] = $value;
            }
        }

        return $config;
    }

    if (! $driverName || $driverName === 'auto') {
        if (extension_loaded('apcu')) {
            $driverName = 'apcu';
        } elseif (extension_loaded('wincache')) {
            $driverName = 'wincache';
        } else {
            $driverName = 'files';
        }
    }

    if (flextype('registry')->get('flextype.settings.cache.enabled') === false) {
        $driverName = 'devnull';
    }

    switch ($driverName) {
        case 'apcu':
            $config = new Config(getDriverConfig($driverName));
            break;
        case 'cassandra':
            $config = new \Phpfastcache\Drivers\Cassandra\Config(getDriverConfig($driverName));
            break;
        case 'cookie':
            $config = new \Phpfastcache\Drivers\Cookie\Config(getDriverConfig($driverName));
            break;
        case 'couchbase':
            $config = new \Phpfastcache\Drivers\Couchbase\Config(getDriverConfig($driverName));
            break;
        case 'couchdb':
            $config = new \Phpfastcache\Drivers\Couchdb\Config(getDriverConfig($driverName));
            break;
        case 'devfalse':
            $config = new \Phpfastcache\Drivers\Devfalse\Config(getDriverConfig($driverName));
            break;
        case 'devnull':
            $config = new \Phpfastcache\Drivers\Devnull\Config(getDriverConfig($driverName));
            break;
        case 'devtrue':
            $config = new \Phpfastcache\Drivers\Devtrue\Config(getDriverConfig($driverName));
            break;
        case 'files':
            $config = new \Phpfastcache\Drivers\Files\Config(getDriverConfig($driverName));
            break;
        case 'leveldb':
            $config = new \Phpfastcache\Drivers\Leveldb\Config(getDriverConfig($driverName));
            break;
        case 'memcache':
            $config = new \Phpfastcache\Drivers\Memcache\Config(getDriverConfig($driverName));
            break;
        case 'memcached':
            $config = new \Phpfastcache\Drivers\Memcached\Config(getDriverConfig($driverName));
            break;
        case 'memstatic':
            $config = new \Phpfastcache\Drivers\Memstatic\Config(getDriverConfig($driverName));
            break;
        case 'mongodb':
            $config = new \Phpfastcache\Drivers\Mongodb\Config(getDriverConfig($driverName));
            break;
        case 'predis':
            $config = new \Phpfastcache\Drivers\Predis\Config(getDriverConfig($driverName));
            break;
        case 'redis':
            $config = new \Phpfastcache\Drivers\Redis\Config(getDriverConfig($driverName));
            break;
        case 'riak':
            $config = new \Phpfastcache\Drivers\Riak\Config(getDriverConfig($driverName));
            break;
        case 'sqlite':
            $config = new \Phpfastcache\Drivers\Sqlite\Config(getDriverConfig($driverName));
            break;
        case 'ssdb':
            $config = new \Phpfastcache\Drivers\Ssdb\Config(getDriverConfig($driverName));
            break;
        case 'wincache':
            $config = new \Phpfastcache\Drivers\Wincache\Config(getDriverConfig($driverName));
            break;
        case 'zenddisk':
            $config = new \Phpfastcache\Drivers\Zenddisk\Config(getDriverConfig($driverName));
            break;
        case 'zendshm':
            $config = new \Phpfastcache\Drivers\Zendshm\Config(getDriverConfig($driverName));
            break;
        default:
            // code...
            break;
    }

    return new Cache($driverName, $config);
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

flextype('markdown')->getInstance()->setBreaksEnabled(flextype('registry')->get('flextype.settings.markdown.auto_line_breaks'));
flextype('markdown')->getInstance()->setUrlsLinked(flextype('registry')->get('flextype.settings.markdown.auto_url_links'));
flextype('markdown')->getInstance()->setMarkupEscaped(flextype('registry')->get('flextype.settings.markdown.escape_markup'));

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
    $source = new Flysystem(
        new Local(PATH['project'] . '/uploads/entries/')
    );

    // Set cache filesystem
    $cache = new Flysystem(
        new Local(PATH['tmp'] . '/glide')
    );

    // Set watermarks filesystem
    $watermarks = new Flysystem(
        new Local(PATH['project'] . '/watermarks')
    );

    // Set image manager
    $imageManager = new ImageManager($imagesSettings);

    // Set manipulators
    $manipulators = [
        new Orientation(),
        new Crop(),
        new Size(2000 * 2000),
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
