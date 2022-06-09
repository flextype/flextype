<?php

declare(strict_types=1);

// Add endpoints routes
require_once __DIR__ . '/endpoints/tokens.php';
require_once __DIR__ . '/endpoints/cache.php';
require_once __DIR__ . '/endpoints/entries.php';
require_once __DIR__ . '/endpoints/registry.php';

// Add project routes
if (filesystem()->file(PATH_PROJECT . '/routes/routes.php')->exists()) {
    require_once PATH_PROJECT . '/routes/routes.php';
}