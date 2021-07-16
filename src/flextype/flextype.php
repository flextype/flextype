<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Atomastic\Csrf\Csrf;
use Atomastic\Registry\Registry;
use Atomastic\Session\Session;
use Bnf\Slim3Psr15\CallableResolver;
use Cocur\Slugify\Slugify;
use DateTimeZone;
use Flextype\Foundation\Actions;
use Flextype\Foundation\Cors;
use Flextype\Foundation\Content\Content;
use Flextype\Foundation\Flextype;
use Flextype\Foundation\Media\Media;
use Flextype\Foundation\Plugins;
use Flextype\Support\Parsers\Parsers;
use Flextype\Support\Serializers\Serializers;
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
use Phpfastcache\Drivers\Apcu\Config;
use Phpfastcache\Helper\Psr16Adapter as Cache;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

use function date;
use function date_default_timezone_set;
use function error_reporting;
use function extension_loaded;
use function file_exists;
use function flextype;
use function function_exists;
use function get_class;
use function in_array;
use function mb_internal_encoding;
use function mb_language;
use function mb_regex_encoding;
use function str_replace;
use function strings;
use function sys_get_temp_dir;
use function ucwords;

/**
 * Init Registry
 */
$registry = Registry::getInstance();

/**
 * Init Actions
 */
$actions = Actions::getInstance();

/**
 * Preflight the Flextype
 */
include_once ROOT_DIR . '/src/flextype/preflight.php';

/**
 * Create new Flextype Application
 */
$flextype = Flextype::getInstance([
    'settings' => [
        'debug' => $registry->get('flextype.settings.errors.display'),
        'displayErrorDetails' => $registry->get('flextype.settings.display_error_details'),
        'addContentLengthHeader' => $registry->get('flextype.settings.add_content_length_header'),
        'routerCacheFile' => $registry->get('flextype.settings.router_cache_file'),
        'determineRouteBeforeAppMiddleware' => $registry->get('flextype.settings.determine_route_before_app_middleware'),
        'outputBuffering' => $registry->get('flextype.settings.output_buffering'),
        'responseChunkSize' => $registry->get('flextype.settings.response_chunk_size'),
        'httpVersion' => $registry->get('flextype.settings.http_version'),
    ],
]);

/**
 * Display Errors
 */
if ($registry->get('flextype.settings.errors.display')) {
    $environment = new Environment($_SERVER);
    $uri         = Uri::createFromEnvironment($environment);

    $prettyPageHandler = new PrettyPageHandler();

    $prettyPageHandler->setEditor((string) $registry->get('flextype.settings.whoops.editor'));
    $prettyPageHandler->setPageTitle((string) $registry->get('flextype.settings.whoops.page_title'));

    $prettyPageHandler->addDataTable('Flextype Application', [
        'Application Class' => get_class(flextype()),
        'Script Name'       => $environment->get('SCRIPT_NAME'),
        'Request URI'       => $environment->get('PATH_INFO') ?: '<none>',
    ]);

    $prettyPageHandler->addDataTable('Flextype Application (Request)', [
        'Path'            => $uri->getPath(),
        'URL'             => (string) $uri,
        'Query String'    => $uri->getQuery() ?: '<none>',
        'Scheme'          => $uri->getScheme() ?: '<none>',
        'Port'            => $uri->getPort() ?: '<none>',
        'Host'            => $uri->getHost() ?: '<none>',
    ]);

    // Set Whoops to default exception handler
    $whoops = new Run();
    $whoops->pushHandler($prettyPageHandler);

    // Enable JsonResponseHandler when request is AJAX
    if (Misc::isAjaxRequest()) {
        $whoops->pushHandler(new JsonResponseHandler());
    }

    $whoops->register();

    flextype()->container()['whoops'] = $whoops;
} else {
    error_reporting(0);
}

/**
 * Create a standard session hanndler
 */
flextype()->container()['session'] = static fn () => new Session();

/**
 * Supply a custom callable resolver, which resolves PSR-15 middlewares.
 */
flextype()->container()['callableResolver'] = static fn () => new CallableResolver(flextype()->container());

/**
 * Add registry service to Flextype container
 */
flextype()->container()['registry'] = $registry;

/**
 * Add actions service to Flextype container
 */
flextype()->container()['actions'] = $actions;

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
flextype()->container()['emitter'] = static fn () => new Emitter();

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
 * Add parsers service to Flextype container
 */
flextype()->container()['parsers'] = static fn () => new Parsers();

/**
 * Add serializer service to Flextype container
 */
flextype()->container()['serializers'] = static fn () => new Serializers();

/**
 * Add images service to Flextype container
 */
flextype()->container()['images'] = static function () {
    // Get images settings
    $imagesSettings = ['driver' => flextype('registry')->get('flextype.settings.media.image.driver')];

    // Set source filesystem
    $source = new Flysystem(
        new Local(PATH['project'] . '/media/')
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
 * Add content service to Flextype container
 */
flextype()->container()['content'] = static fn () => new Content(flextype('registry')->get('flextype.settings.entries.content'));

/**
 * Add media service to Flextype container
 */
flextype()->container()['media'] = static fn () => new Media();

/**
 * Add plugins service to Flextype container
 */
flextype()->container()['plugins'] = static fn () => new Plugins();

/**
 * Add cors service to Flextype container
 */
flextype()->container()['cors'] = static fn () => new Cors();

/**
 * Set session options before you start the session
 * Standard PHP session configuration options
 * https://secure.php.net/manual/en/session.configuration.php
 */
flextype('session')->setOptions(flextype('registry')->get('flextype.settings.session'));

/**
 * Start the session
 */
flextype('session')->start();

/**
 * Add CSRF (cross-site request forgery) protection service to Flextype container
 */
flextype()->container()['csrf'] = static fn () => new Csrf('__csrf_token', '', 128);

/**
 * Set internal encoding
 */
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding(flextype('registry')->get('flextype.settings.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding(flextype('registry')->get('flextype.settings.charset'));

/**
 * Set default timezone
 */
if (in_array(flextype('registry')->get('flextype.settings.timezone'), DateTimeZone::listIdentifiers())) {
    date_default_timezone_set(flextype('registry')->get('flextype.settings.timezone'));
}

/**
 * Init shortocodes
 *
 * Load Flextype Shortcodes from directory /flextype/Support/Parsers/Shortcodes/ based on flextype.settings.parsers.shortcode.shortcodes array
 */
$shortcodes = flextype('registry')->get('flextype.settings.parsers.shortcode.shortcodes');

foreach ($shortcodes as $shortcodeName => $shortcode) {
    $shortcodeFilePath = ROOT_DIR . '/src/flextype/Support/Parsers/Shortcodes/' . str_replace('_', '', ucwords($shortcodeName, '_')) . 'Shortcode.php';
    if (! file_exists($shortcodeFilePath)) {
        continue;
    }

    include_once $shortcodeFilePath;
}

/**
 * Init plugins
 */
flextype('plugins')->init();

/**
 * Include API ENDPOINTS
 */
include_once ROOT_DIR . '/src/flextype/Endpoints/Utils/errors.php';
include_once ROOT_DIR . '/src/flextype/Endpoints/Utils/access.php';
include_once ROOT_DIR . '/src/flextype/Endpoints/entries.php';
include_once ROOT_DIR . '/src/flextype/Endpoints/registry.php';
include_once ROOT_DIR . '/src/flextype/Endpoints/media.php';
include_once ROOT_DIR . '/src/flextype/Endpoints/images.php';

/**
 * Enable lazy CORS
 *
 * CORS (Cross-origin resource sharing) allows JavaScript web apps to make HTTP requests to other domains.
 * This is important for third party web apps using Flextype, as without CORS, a JavaScript app hosted on example.com
 * couldn't access our APIs because they're hosted on another.com which is a different domain.
 */
flextype('cors')->init();

/**
 * Run high priority event: onFlextypeBeforeRun before Flextype Application starts.
 */
flextype('emitter')->emit('onFlextypeBeforeRun');

/**
 * Run application
 */
flextype()->run();
