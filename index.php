<?php
/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rawilum;

/**
 * Define the application's minimum supported PHP version.
 */
define('RAWILUM_MINIMUM_PHP', '7.1.3');

// Check PHP Version
version_compare($ver = PHP_VERSION, $req = RAWILUM_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Rawilum needs at least <strong>PHP %s</strong> to run.', $ver, $req));

// Ensure vendor libraries exist and Register The Auto Loader
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register the auto-loader.
$loader = require_once $autoload;

// Run Rawilum Application
$rawilum = Rawilum::instance()->run();
