<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation;

use Slim\App;

class Flextype extends App
{
    /**
     * Flextype version
     *
     * @var string
     */
    protected static $version = '0.9.10';

    protected static $instance = null;

    protected static $container = null;

    public function __construct($flextype = [])
    {
        parent::__construct($flextype);

        // Store instance
        self::$instance = $this;

        // Store instance container
        self::$container = self::$instance->getContainer();
    }

    /**
     * Get Dependency Injection Container.
     *
     * @param string $key DI Container key.
     */
    public function container(?string $key = null)
    {
        if ($key !== null) {
            return self::$container[$key];
        }

        return self::$container;
    }

    /**
     * Returns Flextype Instance
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * This method will returns the current Flextype version
     */
    public static function getVersion() : string
    {
        return self::$version;
    }
}
