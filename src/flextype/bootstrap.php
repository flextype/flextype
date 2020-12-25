<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Atomastic\Registry\Registry;
use Flextype\Foundation\Flextype;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;
use DateTimeZone;

use function date_default_timezone_set;
use function error_reporting;
use function file_exists;
use function flextype;
use function function_exists;
use function get_class;
use function mb_internal_encoding;
use function mb_language;
use function mb_regex_encoding;
use function str_replace;
use function ucwords;

/**
 * Init Registry
 */
$registry = Registry::getInstance();

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
 * Include Dependencies
 */
include_once 'dependencies.php';

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
 * Load Flextype Shortcodes from directory /flextype/Support/Parsers/Shortcodes/ based on flextype.settings.shortcode.shortcodes array
 */
$shortcodes = flextype('registry')->get('flextype.settings.shortcode.shortcodes');

foreach ($shortcodes as $shortcodeName => $shortcode) {
    $shortcodeFilePath = ROOT_DIR . '/src/flextype/Support/Parsers/Shortcodes/' . str_replace('_', '', ucwords($shortcodeName, '_')) . 'Shortcode.php';
    if (! file_exists($shortcodeFilePath)) {
        continue;
    }

    include_once $shortcodeFilePath;
}

/**
 * Init entries fields
 *
 * Load Flextype Entries fields from directory /flextype/Foundation/Entries/Fields/ based on flextype.settings.entries.fields array
 */
$entryFields = flextype('registry')->get('flextype.settings.entries.fields');

foreach ($entryFields as $fieldName => $field) {
    $entryFieldFilePath = ROOT_DIR . '/src/flextype/Foundation/Entries/Fields/' . str_replace('_', '', ucwords($fieldName, '_')) . 'Field.php';
    if (! file_exists($entryFieldFilePath)) {
        continue;
    }

    include_once $entryFieldFilePath;
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
