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
    const VERSION = '0.9.11';

    /**
     * The Flextype's instance is stored in a static field. This field is an
     * array, because we'll allow our Flextype to have subclasses. Each item in
     * this array will be an instance of a specific Flextype's subclass.
     *
     * @var array
     */
    private static $instances = [];

    /**
     * Flextype should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Flextype should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a Flextype.");
    }

    /**
     * Flextype construct
     *
     * @param ContainerInterface|array $container
     */
    protected function __construct($container = [])
    {
        parent::__construct($container);
    }

    /**
     * Get/Set Dependency Injection Container.
     *
     * @param string|null $name DI Container name.
     */
    public function container(?string $name = null)
    {
        if (is_null($name)) {
            return self::getInstance()->getContainer();
        }

        return self::getInstance()->getContainer()[$name];
    }

    /**
     * Returns Flextype Instance
     *
     * @param ContainerInterface|array $container
     */
     public static function getInstance($container = []) : Flextype
     {
         $cls = static::class;
         if (!isset(self::$instances[$cls])) {
             self::$instances[$cls] = new static($container);
         }

         return self::$instances[$cls];
     }

    /**
     * Returns the current Flextype version
     */
    public function getVersion() : string
    {
        return static::VERSION;
    }
}
