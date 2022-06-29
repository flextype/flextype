<?php

declare(strict_types=1);

if (filesystem()->file(PATH_PROJECT . '/bootstrap/before-plugins.php')->exists()) {
    require_once PATH_PROJECT . '/bootstrap/before-plugins.php';
}