<?php

/**
 * @package Flextype
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\{Http\Http, Session\Session, ErrorHandler\ErrorHandler, Registry\Registry, Filesystem\Filesystem};
use Symfony\Component\Yaml\Yaml;

class Flextype
{
    /**
     * An instance of the Flextype class
     *
     * @var object
     * @access protected
     */
    protected static $instance = null;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * The version of Flextype
     *
     * @var string
     */
    const VERSION = '0.3.0';

    /**
     * Constructor.
     *
     * @access protected
     */
    protected function __construct()
    {
        static::init();
    }

    /**
     * Init Application
     *
     * @access protected
     */
    protected static function init() : void
    {
        // Turn on output buffering
        ob_start();

        // Set empty site item
        Registry::set('site', []);

        // Set site items if site config exists
        if (Filesystem::fileExists($site_config = CONFIG_PATH . '/' . 'site.yaml')) {
            Registry::set('site', Yaml::parseFile($site_config));
        } else {
            throw new \RuntimeException("Flextype site config file does not exist.");
        }

        // Display Errors
        if (Registry::get('site.errors.display')) {
            define('DEVELOPMENT', true);
            error_reporting(-1);
        } else {
            define('DEVELOPMENT', false);
            error_reporting(0);
        }

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(Registry::get('site.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding(Registry::get('site.charset'));

        // Set Error handler
        set_error_handler('Flextype\Component\ErrorHandler\ErrorHandler::error');
        register_shutdown_function('Flextype\Component\ErrorHandler\ErrorHandler::fatal');
        set_exception_handler('Flextype\Component\ErrorHandler\ErrorHandler::exception');

        // Set default timezone
        date_default_timezone_set(Registry::get('site.timezone'));

        // Start the session
        Session::start();

        // Create Cache Instance
        Cache::instance();

        // Create Shortcodes Instance
        Shortcodes::instance();

        // Create Themes Instance
        Themes::instance();

        // Create Plugins Instance
        Plugins::instance();

        // Create Pages Instance
        Pages::instance();

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
     * Return the Flextype instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
     public static function instance()
     {
         return !isset(self::$instance) and self::$instance = new Flextype();
     }
}
