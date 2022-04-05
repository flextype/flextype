<?php

declare(strict_types=1);

namespace Flextype;

define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));
define('PATH', [
    'project'   => ROOT_DIR . '/project',
    'tmp'   => ROOT_DIR . '/var/tmp',
]);

! is_file($flextype_autoload = ROOT_DIR . '/vendor/autoload.php') and exit('Please run: <i>composer install</i> for flextype');
$flextype_loader = require_once $flextype_autoload;

filesystem()->directory(PATH['tmp'])->exists() and filesystem()->directory(PATH['tmp'])->delete();
filesystem()->directory(ROOT_DIR . '/project/config/flextype/')->ensureExists(0755, true);
filesystem()->file(ROOT_DIR . '/tests/fixtures/settings/settings.yaml')->copy(ROOT_DIR . '/project/config/flextype/settings.yaml');

include ROOT_DIR . '/src/flextype/flextype.php';