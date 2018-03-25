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
use Url;

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
            return Url::getBase();
        });
    }

    /**
     * Initialize Flextype Shortcodes
     *
     * @access public
     * @return object
     */
    public static function init()
    {
        return !isset(self::$instance) and self::$instance = new Shortcodes();
    }
}
