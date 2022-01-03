<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Glowy\Arrays\Arrays;

class Actions extends Arrays
{
    /**
     * Actions instance
     */
    private static ?Actions $instance = null;

    /**
     * Actions registry storage
     */
    private static ?Arrays $registry = null;

    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Actions
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        if (static::$registry === null) {
            static::$registry = new Arrays();
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