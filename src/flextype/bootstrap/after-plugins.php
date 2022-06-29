<?php

declare(strict_types=1);

if (filesystem()->file(PATH_PROJECT . '/bootstrap/after-plugins.php')->exists()) {
    require_once PATH_PROJECT . '/bootstrap/after-plugins.php';
}