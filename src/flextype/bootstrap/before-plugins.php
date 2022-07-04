<?php

declare(strict_types=1);

if (filesystem()->file(FLEXTYPE_PATH_PROJECT . '/bootstrap/before-plugins.php')->exists()) {
    require_once FLEXTYPE_PATH_PROJECT . '/bootstrap/before-plugins.php';
}