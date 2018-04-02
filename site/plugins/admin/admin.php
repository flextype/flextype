<?php

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Url;
use Arr;
use Response;
use Request;

//
// Add listner for onPageBeforeRender event
//
if (Url::getUriSegment(0) == 'admin') {
    Events::addListener('onPageBeforeRender', function () {
        Admin::instance();
    });
}


class Admin {

    /**
     * An instance of the Admin class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    protected static function init()
    {
        if (static::isLoggedIn()) {
            echo 'ADMIN AREA';
        } else {
            echo 'LOGIN AREA';
        }

        Request::shutdown();
    }

    protected static function isLoggedIn() : bool
    {
        return false;
    }

    /**
     * Return the Admin instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Admin();
    }
}
