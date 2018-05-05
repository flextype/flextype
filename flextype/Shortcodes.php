<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Thunder\Shortcode\ShortcodeFacade;
use Flextype\Component\Http\Http;

class Shortcodes
{

    /**
     * An instance of the Shortcodes class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * ShortcodeFacade Driver
     *
     * @var ShortcodeFacade
     */
    protected static $driver;

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    /**
     * Init Shortcodes
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        // Set driver
        static::$driver = new ShortcodeFacade();

        // Register Default Shortcodes
        static::registerDefaultShortcodes();
    }

    /**
     * Returns driver variable
     *
     * @access public
     * @return object
     */
    public static function driver() : ShortcodeFacade
    {
        return static::$driver;
    }

    /**
     * Register default shortcodes
     *
     * @access protected
     */
    protected static function registerDefaultShortcodes() : void
    {
        static::driver()->addHandler('site_url', function() {
            return Http::getBaseUrl();
        });
    }

    /**
     * Return the Shortcodes instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Shortcodes();
    }
}
