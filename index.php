<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

// Define the application minimum supported PHP version.
define('FLEXTYPE_MINIMUM_PHP', '7.1.3');

// Check PHP Version
version_compare($ver = PHP_VERSION, $req = FLEXTYPE_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Flextype needs at least <strong>PHP %s</strong> to run.', $ver, $req));

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

// Init Flextype
Flextype::instance();
