<?php

declare(strict_types=1);

namespace Flextype;

use function Glowy\Filesystem\filesystem;

// Add endpoints routes
require_once __DIR__ . '/endpoints/tokens.php';
require_once __DIR__ . '/endpoints/cache.php';
require_once __DIR__ . '/endpoints/entries.php';
require_once __DIR__ . '/endpoints/registry.php';

// Add project routes
if (filesystem()->file(FLEXTYPE_PATH_PROJECT . '/routes/routes.php')->exists()) {
    require_once FLEXTYPE_PATH_PROJECT . '/routes/routes.php';
}