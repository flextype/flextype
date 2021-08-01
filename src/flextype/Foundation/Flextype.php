<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use Exception;
use Psr\Container\ContainerInterface;
use Slim\App;

final class Flextype
{
    /**
     * Flextype version
     */
    public const VERSION = '0.9.16';

    /**
     * The Flextype instance.
     *
     * @var array
     */
    private static ?Flextype $instance = null;

    /**
     * The Flextype Application.
     */
    private static App $app;

    /**
     * The Flextype Application Container.
     */
    private static Container $container;

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
     * Flextype construct.
     */
    protected function __construct(?ContainerInterface $container = null)
    {
        // Set Application with PHP-DI Bridge
        self::$app = Bridge::create($container);

        // Set Application Container
        self::$container = self::$app->getContainer();
    }

    /**
     * Get Flextype Application.
     *
     * @return App Returns Flextype Application.
     *
     * @access public
     */
    public function app(): App
    {
        return self::$app;
    }

    /**
     * Get Flextype Application Container.
     *
     * @return Flextype Returns Flextype Application Container.
     *
     * @access public
     */
    public function container(): Container
    {
        return self::$container;
    }

    /**
     * Returns Flextype Instance.
     *
     * Gets the instance via lazy initialization (created on first usage)
     *
     * @return Flextype Returns the current Flextype Instance.
     *
     * @access public
     */
    public static function getInstance(?ContainerInterface $container = null): Flextype
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Get the current Flextype version.
     *
     * @return string Returns the current Flextype version.
     *
     * @access public
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }
}
