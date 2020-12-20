<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Exception;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Uri;

use function is_null;

final class Flextype extends App
{
    /**
     * Flextype version
     */
    public const VERSION = '0.9.13';

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
    protected function __clone()
    {
        throw new Exception('Cannot clone a Flextype.');
    }

    /**
     * Flextype should not be restorable from strings.
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize a Flextype.');
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
     * @param ContainerInterface|array $container Container.
     */
    public static function getInstance($container = []): Flextype
    {
        $cls = static::class;
        if (! isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($container);
        }

        return self::$instances[$cls];
    }

    /**
     * Determine API Request
     *
     * @return bool
     */
    public function isApiRequest(): bool
    {
        return explode('/', Uri::createFromEnvironment(new Environment($_SERVER))->getPath())[0] === 'api';
    }

    /**
     * Returns the current Flextype version
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }
}
