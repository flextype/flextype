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

// Define the path to the root directory (without trailing slash).
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

// Define the path to the site directory (without trailing slash).
define('SITE_PATH', ROOT_DIR . '/site');

// Define the path to the pages directory (without trailing slash).
define('PAGES_PATH', SITE_PATH . '/pages');

// Define the path to the themes directory (without trailing slash).
define('THEMES_PATH', SITE_PATH . '/themes');

// Define the path to the plugins directory (without trailing slash).
define('PLUGINS_PATH', SITE_PATH . '/plugins');

// Define the path to the config directory (without trailing slash).
define('CONFIG_PATH', SITE_PATH . '/config');

// Define the path to the cache directory (without trailing slash).
define('CACHE_PATH', SITE_PATH . '/cache');

// Define the path to the logs directory (without trailing slash).
define('LOGS_PATH', SITE_PATH . '/logs');

// Define the path to the logs directory (without trailing slash).
define('ACCOUNTS_PATH', SITE_PATH . '/accounts');
