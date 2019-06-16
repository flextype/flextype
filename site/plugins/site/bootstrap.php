<?php

namespace Flextype;

// Ensure vendor libraries exist
!is_file($autoload = __DIR__ . '/vendor/autoload.php') and exit("Please run: <i>composer install</i>");

// Register The Auto Loader
$loader = require_once $autoload;

// Include routes
include_once 'routes/web.php';

// Include dependencies
include_once 'dependencies.php';
