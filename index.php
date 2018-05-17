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

// Define the path to the root directory (without trailing slash).
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

// Define the PATH (without trailing slash).
define('PATH', ['site'     => ROOT_DIR . '/site',
                'plugins'  => ROOT_DIR . '/site/plugins',
                'themes'   => ROOT_DIR . '/site/themes',
                'pages'    => ROOT_DIR . '/site/pages',
                'blocks'   => ROOT_DIR . '/site/blocks',
                'data'     => ROOT_DIR . '/site/data',
                'config'   => ROOT_DIR . '/site/config',
                'cache'    => ROOT_DIR . '/site/cache',
                'accounts' => ROOT_DIR . '/site/accounts']);

// Define the path to the logs directory (without trailing slash).
define('LOGS_PATH', PATH['site'] . '/logs');

// Check PHP Version
version_compare($ver = PHP_VERSION, $req = FLEXTYPE_MINIMUM_PHP, '<') and exit(sprintf('You are running PHP %s, but Flextype needs at least <strong>PHP %s</strong> to run.', $ver, $req));

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

// Init Flextype Application
Flextype::instance();
