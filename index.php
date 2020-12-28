<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use function define;
use function getcwd;
use function is_file;
use function sprintf;
use function str_replace;
use function version_compare;
use const DIRECTORY_SEPARATOR;
use const PHP_VERSION;

/**
 * Define the application minimum supported PHP version.
 */
define('FLEXTYPE_MINIMUM_PHP', '7.4.0');

/**
 * Define the PATH to the root directory (without trailing slash).
 */
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

/**
 * Define the PATH (without trailing slash).
 */
define('PATH', [
    'project' => ROOT_DIR . '/project',
    'tmp'     => ROOT_DIR . '/var/tmp',
]);

/**
 * Check PHP Version
 */
version_compare($ver = PHP_VERSION, $req = FLEXTYPE_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Flextype needs at least <strong>PHP %s</strong> to run.', $ver, $req));

/**
 * Ensure vendor libraries exist
 */
! is_file($flextypeAutoload = __DIR__ . '/vendor/autoload.php') and exit('Please run: <i>composer install</i> for flextype');

/**
 * Register The Auto Loader
 *
 * Composer provides a convenient, automatically generated class loader for
 * our application. We just need to utilize it! We'll simply require it
 * into the script here so that we don't have to worry about manual
 * loading any of our classes later on. It feels nice to relax.
 * Register The Auto Loader
 */
$flextypeLoader = require_once $flextypeAutoload;

/**
 * Bootstraps the Flextype
 *
 * This bootstraps the Flextype and gets it ready for use, then it
 * will load up this application so that we can run it and send
 * the responses back to the browser and delight our users.
 */
include __DIR__ . '/src/flextype/bootstrap.php';
