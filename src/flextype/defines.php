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

if (! defined('FLEXTYPE_MINIMUM_PHP')) {
    /**
     * Define the Flextype Application minimum supported PHP version.
     */
    define('FLEXTYPE_MINIMUM_PHP', '7.4.0');
}

if (! defined('FLEXTYPE_PROJECT_NAME')) {
    /**
     * Define the project name.
     */
    define('FLEXTYPE_PROJECT_NAME', 'project');
}

if (! defined('FLEXTYPE_PATH_PROJECT')) {
    /**
     * Define the project path (without trailing slash).
     */
    define('FLEXTYPE_PATH_PROJECT', FLEXTYPE_ROOT_DIR . '/' . FLEXTYPE_PROJECT_NAME);
}

if (! defined('FLEXTYPE_PATH_TMP')) {
    /**
     * Define the project tmp path (without trailing slash).
     */
    define('FLEXTYPE_PATH_TMP', FLEXTYPE_ROOT_DIR . '/var/tmp');
}