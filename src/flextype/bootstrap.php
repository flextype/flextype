<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\App\Foundation\Flextype;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Session\Session;
use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;
use function date_default_timezone_set;
use function error_reporting;
use function file_exists;
use function function_exists;
use function mb_internal_encoding;
use function mb_language;
use function mb_regex_encoding;
use function str_replace;
use function ucwords;

/**
 * Start the session
 */
Session::start();

/**
 * Init Registry
 */
$registry = new Registry();

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
        'whoops.editor' => $registry->get('flextype.settings.whoops.editor'),
        'whoops.page_title' => $registry->get('flextype.settings.whoops.page_title'),
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
 * Include Dependencies
 */
include_once 'dependencies.php';

/**
 * Include API ENDPOINTS
 */
include_once ROOT_DIR . '/src/flextype/app/Endpoints/Utils/errors.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/Utils/access.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/entries.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/registry.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/files.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/folders.php';
include_once ROOT_DIR . '/src/flextype/app/Endpoints/images.php';

/**
 * Set internal encoding
 */
function_exists('mb_language') and mb_language('uni');
function_exists('mb_regex_encoding') and mb_regex_encoding(flextype('registry')->get('flextype.settings.charset'));
function_exists('mb_internal_encoding') and mb_internal_encoding(flextype('registry')->get('flextype.settings.charset'));

/**
 * Display Errors
 */
if (flextype('registry')->get('flextype.settings.errors.display')) {

    /**
     * Add WhoopsMiddleware
     */
    flextype()->add(new WhoopsMiddleware());
} else {
    error_reporting(0);
}

/**
 * Set default timezone
 */
date_default_timezone_set(flextype('registry')->get('flextype.settings.timezone'));

/**
 * Init shortocodes
 *
 * Load Flextype Shortcodes from directory /flextype/app/Support/Parsers/Shortcodes/ based on flextype.settings.shortcode.shortcodes array
 */
$shortcodes = flextype('registry')->get('flextype.settings.shortcode.shortcodes');

foreach ($shortcodes as $shortcode_name => $shortcode) {
    $shortcode_file_path = ROOT_DIR . '/src/flextype/app/Support/Parsers/Shortcodes/' . str_replace('_', '', ucwords($shortcode_name, '_')) . 'Shortcode.php';
    if (! file_exists($shortcode_file_path)) {
        continue;
    }

    include_once $shortcode_file_path;
}

/**
 * Init entries fields
 *
 * Load Flextype Entries fields from directory /flextype/app/Foundation/Entries/Fields/ based on flextype.settings.entries.fields array
 */
$entry_fields = flextype('registry')->get('flextype.settings.entries.fields');

foreach ($entry_fields as $field_name => $field) {
    $entry_field_file_path = ROOT_DIR . '/src/flextype/app/Foundation/Entries/Fields/' . str_replace('_', '', ucwords($field_name, '_')) . 'Field.php';
    if (! file_exists($entry_field_file_path)) {
        continue;
    }

    include_once $entry_field_file_path;
}

/**
 * Init plugins
 */
flextype('plugins')->init();

/**
 * Enable lazy CORS
 *
 * CORS (Cross-origin resource sharing) allows JavaScript web apps to make HTTP requests to other domains.
 * This is important for third party web apps using Flextype, as without CORS, a JavaScript app hosted on example.com
 * couldn't access our APIs because they're hosted on another.com which is a different domain.
 */
flextype('cors')->init();

/**
 * Run application
 */
flextype()->run();
