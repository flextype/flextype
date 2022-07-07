<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

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
