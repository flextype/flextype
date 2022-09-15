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

use function Glowy\Filesystem\filesystem;

if (filesystem()->file(FLEXTYPE_PATH_PROJECT . '/bootstrap/before-plugins.php')->exists()) {
    require_once FLEXTYPE_PATH_PROJECT . '/bootstrap/before-plugins.php';
}
