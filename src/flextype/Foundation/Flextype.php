<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Exception;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Slim\App;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\OutputBufferingMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Psr7\Factory\StreamFactory;
use Atomastic\Csrf\Csrf;
use Atomastic\Registry\Registry;
use Atomastic\Session\Session;
use Cocur\Slugify\Slugify;
use DateTimeZone;
use Flextype\Foundation\Actions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;

use function is_null;

final class Flextype
{
    /**
     * Flextype version
     */
    public const VERSION = '0.9.16';

    /**
     * The Flextype's instance is stored in a static field. This field is an
     * array, because we'll allow our Flextype to have subclasses. Each item in
     * this array will be an instance of a specific Flextype's subclass.
     *
     * @var array
     */
    private static array $instances = [];

    private App $app;
    private Container $container;

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
     */
    protected function __construct(ContainerInterface $container = null)
    {
        $this->app       = Bridge::create($container);
        $this->container = $this->app->getContainer();
    }

    public function app() 
    {
        return $this->app;
    }

    public function container() 
    {
        return $this->container;
    }


    /**
     * Returns Flextype Instance
     */
    public static function getInstance(ContainerInterface $container = null): Flextype
    {
        $cls = static::class;
        if (! isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static($container);
        }

        return self::$instances[$cls];
    }

    /**
     * Returns the current Flextype version
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }
}
