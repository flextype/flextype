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
    public const VERSION = '1.0.0-alpha.3';

    /**
     * The Flextype instance.
     */
    private static Flextype|null $instance = null;

    /**
     * The Flextype Application.
     */
    private static App $app;

    /**
     * The Flextype Application Container.
     */
    private static ContainerInterface $container;

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
     * @return ContainerInterface Returns Flextype Application Container.
     *
     * @access public
     */
    public function container(): ContainerInterface
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
            static::$instance = new self($container);
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
