<?php

declare(strict_types=1);

// Add endpoints routes
require_once __DIR__ . '/endpoints/utils.php';
require_once __DIR__ . '/endpoints/entries.php';
require_once __DIR__ . '/endpoints/registry.php';

// Add project routes
if (filesystem()->file(PATH['project'] . '/routes/routes.php')->exists()) {
    require_once PATH['project'] . '/routes/routes.php';
}