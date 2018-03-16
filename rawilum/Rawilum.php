<?php namespace Rawilum;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Url;
use Session;

 /**
  * @package Rawilum
  *
  * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
  * @link http://rawilum.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

class Rawilum
{
    /**
     * An instance of the Fansoro class
     *
     * @var object
     * @access protected
     */
    protected static $instance = null;

    public static $filesystem = null;
    public static $finder = null;
    public static $parsedown = null;

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
     * The version of Rawilum
     *
     * @var string
     */
    const VERSION = '0.0.0';

    /**
     * Init Rawilum Application
     *
     * @access protected
     */
    /*protected static function init()
    {
        // Create container
        $container = new static();

        // Define markdown service
        $container['markdown'] = function ($c) {
            return new ParsedownExtra();
        };

        // Define filesystem service
        $container['filesystem'] = function ($c) {
            return new Filesystem();
        };

        // Define finder service
        $container['finder'] = function ($c) {
            return new Finder();
        };

        // Define cache service
        $container['cache'] = function ($c) {
            return new Cache($c);
        };

        // Define config service
        $container['config'] = function ($c) {
            return new Config($c);
        };

        // Define shortcodes service
        $container['shortcodes'] = function ($c) {
            return new Shortcodes($c);
        };

        // Define events service
        $container['events'] = function ($c) {
            return new Events($c);
        };

        // Define filters service
        $container['filters'] = function ($c) {
            return new Filters($c);
        };

        // Define i18n service
        $container['i18n'] = function ($c) {
            return new I18n($c);
        };

        // Define plugins service
        $container['plugins'] = function ($c) {
            return new Plugins($c);
        };

        // Define pages service
        $container['pages'] = function ($c) {
            return new Pages($c);
        };

        // Define themes service
        $container['themes'] = function ($c) {
            return new Themes($c);
        };

        // Init I18n
        $container['i18n']->init();

        // Init Plugins
        $container['plugins']->init();

        // Get current page
        $container['pages']->getPage(Url::getUriString());

        // Return container
        return $container;
    }*/

    /**
     * Constructor.
     *
     * @access protected
     */
    protected function __construct()
    {

        static::$finder     = new Finder();
        static::$filesystem = new Filesystem();

        // Init Config
        Config::init();

        // Turn on output buffering
        ob_start();

        // Display Errors
        if (Config::get('site.errors.display')) {
            define('DEVELOPMENT', true);
            error_reporting(-1);
        } else {
            define('DEVELOPMENT', false);
            error_reporting(0);
        }

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(Config::get('site.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding(Config::get('site.charset'));

        // Set Error handler
        set_error_handler('ErrorHandler::error');
        register_shutdown_function('ErrorHandler::fatal');
        set_exception_handler('ErrorHandler::exception');

        // Set default timezone
        date_default_timezone_set(Config::get('site.timezone'));

        // Start the session
        Session::start();

        // Init Cache
        Cache::init();

        // Init I18n
        I18n::init();

        // Init Plugins
        Plugins::init();

        // Render current page
        Pages::init();

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
      * Initialize Rawilum Application
      *
      *  <code>
      *      Rawium::init();
      *  </code>
      *
      * @access public
      * @return object
      */
     public static function init()
     {
         return !isset(self::$instance) and self::$instance = new Rawilum();
     }
}
