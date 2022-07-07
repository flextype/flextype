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

use Glowy\Arrays\Arrays as Collection;

class Actions extends Collection
{
    /**
     * Actions instance
     */
    protected static Actions|null $instance = null;

    /**
     * Actions registry storage
     */
    protected static Collection|null $registry = null;

    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Actions
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        if (static::$registry === null) {
            static::$registry = new Collection();
        }

        return static::$instance;
    }

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances,
     * to use the Registry, you have to obtain the instance from Registry::getInstance() instead.
     */
    protected function __construct()
    {
    }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it)
     */
    protected function __clone()
    {
    }

    /**
     * Prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup(): void
    {
    }
}
