<?php

namespace Flextype;

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

include_once 'routes.php';

$flextype['SiteController'] = function($container) {
    return new SiteController($container);
};
