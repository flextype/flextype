<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;
use function define;
use function getcwd;
use function is_file;
use function sprintf;
use function str_replace;
use function version_compare;

/**
 * Define the application minimum supported PHP version.
 */
define('FLEXTYPE_MINIMUM_PHP', '7.2.0');

/**
 * Define the PATH to the root directory (without trailing slash).
 */
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

/**
 * Define the PATH (without trailing slash).
 */
define('PATH', [
    'site'      => ROOT_DIR . '/site',
    'plugins'   => ROOT_DIR . '/site/plugins',
    'themes'    => ROOT_DIR . '/site/themes',
    'entries'   => ROOT_DIR . '/site/entries',
    'snippets'  => ROOT_DIR . '/site/snippets',
    'fieldsets' => ROOT_DIR . '/site/fieldsets',
    'config'    => [
        'default' => ROOT_DIR . '/flextype/config',
        'site'    => ROOT_DIR . '/site/config',
    ],
    'cache'     => ROOT_DIR . '/site/cache',
]);

/**
 * Check PHP Version
 */
version_compare($ver = PHP_VERSION, $req = FLEXTYPE_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Flextype needs at least <strong>PHP %s</strong> to run.', $ver, $req));

/**
 * Ensure vendor libraries exist
 */
! is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit('Please run: <i>composer install</i>');

/**
 * Register The Auto Loader
 *
 * Composer provides a convenient, automatically generated class loader for
 * our application. We just need to utilize it! We'll simply require it
 * into the script here so that we don't have to worry about manual
 * loading any of our classes later on. It feels nice to relax.
 * Register The Auto Loader
 */
$loader = require_once $autoload;

/**
 * Bootstraps the Flextype
 *
 * This bootstraps the Flextype and gets it ready for use, then it
 * will load up this application so that we can run it and send
 * the responses back to the browser and delight our users.
 */
include 'flextype/bootstrap.php';
