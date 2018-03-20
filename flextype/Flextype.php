<?php namespace Flextype;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Url;
use Session;

 /**
  * @package Flextype
  *
  * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
  * @link http://flextype.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

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
     * Filesystem
     *
     * @var object
     * @access public
     */
    public static $filesystem = null;

    /**
     * Finder
     *
     * @var object
     * @access public
     */
    public static $finder = null;

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
    const VERSION = '0.1.0';

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

        // Init Themes
        Themes::init();

        // Init Plugins
        Plugins::init();

        // Render current page
        Pages::init();

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
      * Initialize Flextype Application
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
         return !isset(self::$instance) and self::$instance = new Flextype();
     }
}
