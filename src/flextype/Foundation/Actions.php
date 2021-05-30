<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;

class Actions extends Arrays
{
    use Macroable;

    /**
     * Actions instance
     */
    private static ?Actions $instance = null;

    /**
     * Actions storage
     */
    private static ?Arrays $storage = null;

    /**
     * Gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): Actions
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        if (static::$storage === null) {
            static::$storage = new Arrays();
        }

        return static::$instance;
    }

    /**
     * Is not allowed to call from outside to prevent from creating multiple instances,
     * to use the Actions, you have to obtain the instance from Actions::getInstance() instead.
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