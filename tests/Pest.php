<?php

declare(strict_types=1);

namespace Flextype;

define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));

/**
 * Define the project name.
 */
define('FLEXTYPE_PROJECT_NAME', 'project');

/**
 * Define the PATH (without trailing slash).
 */
define('FLEXTYPE_PATH_PROJECT', ROOT_DIR . '/' . FLEXTYPE_PROJECT_NAME);
define('FLEXTYPE_PATH_TMP', ROOT_DIR . '/var/tmp');


! is_file($flextype_autoload = ROOT_DIR . '/vendor/autoload.php') and exit('Please run: <i>composer install</i> for flextype');
$flextype_loader = require_once $flextype_autoload;

filesystem()->directory(FLEXTYPE_PATH_TMP)->exists() and filesystem()->directory(FLEXTYPE_PATH_TMP)->delete();
filesystem()->directory(ROOT_DIR . '/project/config/flextype/')->ensureExists(0755, true);
filesystem()->file(ROOT_DIR . '/tests/fixtures/settings/settings.yaml')->copy(ROOT_DIR . '/project/config/flextype/settings.yaml');

include ROOT_DIR . '/src/flextype/flextype.php';