<?php

declare(strict_types=1);

namespace Flextype;


define('FLEXTYPE_MINIMUM_PHP', '7.3.0');
define('ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', getcwd()));
define('PATH', [
    'project'   => ROOT_DIR . '/project',
    'tmp'   => ROOT_DIR . '/var/tmp',
]);

! is_file($flextype_autoload = ROOT_DIR . '/vendor/autoload.php') and exit('Please run: <i>composer install</i> for flextype');
$flextype_loader = require_once $flextype_autoload;

include ROOT_DIR . '/src/flextype/bootstrap.php';
