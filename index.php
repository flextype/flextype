<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype;

use function getcwd;
use function is_file;
use function sprintf;
use function str_replace;

/**
 * Define the PATH to the root directory (without trailing slash).
 */
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

/**
 * Define the project name.
 */
define('PROJECT_NAME', 'project');

/**
 * Define the PATH (without trailing slash).
 */
define('PATH_PROJECT', ROOT_DIR . '/' . PROJECT_NAME);
define('PATH_TMP', ROOT_DIR . '/var/tmp');

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
require_once __DIR__ . '/src/flextype/flextype.php';
