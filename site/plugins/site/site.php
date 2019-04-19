<?php

namespace Flextype;

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

// Include routes
include_once 'routes.php';

/**
 * Add site controller to Flextype container
 */
$flextype['SiteController'] = function($container) {
    return new SiteController($container);
};
