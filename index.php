<?php namespace Rawilum;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Define the application minimum supported PHP version.
define('RAWILUM_MINIMUM_PHP', '7.1.3');

// Check PHP Version
version_compare($ver = PHP_VERSION, $req = RAWILUM_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Rawilum needs at least <strong>PHP %s</strong> to run.', $ver, $req));

// Ensure vendor libraries exist and Register The Auto Loader
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register the auto-loader.
$loader = require_once $autoload;

// Initialize Rawilum Application
Rawilum::init();
