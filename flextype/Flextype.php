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

use Flextype\Component\Http\Http;
use Flextype\Component\Session\Session;
use Flextype\Component\ErrorHandler\ErrorHandler;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Flextype
{
    /**
     * The version of Flextype
     *
     * @var string
     */
    const VERSION = '0.7.2';

    /**
     * An instance of the Flextype class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    private function __construct()
    {
        Flextype::init();
    }

    /**
     * Init Flextype Application
     *
     * @access private
     */
    private static function init() : void
    {
        // Turn on output buffering
        ob_start();

        Flextype::setConfig();

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding(Registry::get('settings.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding(Registry::get('settings.charset'));

        // Set error handler
        Flextype::setErrorHandler();

        // Set default timezone
        date_default_timezone_set(Registry::get('settings.timezone'));

        // Start the session
        Session::start();

        // Get Cache Instance
        Cache::getInstance();

        // Get Themes Instance
        Themes::getInstance();

        // Get Plugins Instance
        Plugins::getInstance();

        // Get Content Instance
        Content::getInstance();

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
     * Set error handler
     *
     * @access private
     */
    private static function setErrorHandler() : void
    {
        // Display Errors
        if (Registry::get('settings.errors.display')) {
            define('DEVELOPMENT', true);
            error_reporting(-1);
        } else {
            define('DEVELOPMENT', false);
            error_reporting(0);
        }

        // Create directory for logs
        !Filesystem::fileExists(LOGS_PATH) and Filesystem::createDir(LOGS_PATH);

        // Set Error handler
        set_error_handler('Flextype\Component\ErrorHandler\ErrorHandler::error');
        register_shutdown_function('Flextype\Component\ErrorHandler\ErrorHandler::fatal');
        set_exception_handler('Flextype\Component\ErrorHandler\ErrorHandler::exception');
    }

    /**
     * Set config
     *
     * @access private
     */
    private static function setConfig() : void
    {
        // Set empty site item
        Registry::set('settings', []);

        // Set settings items if settings config exists
        if (Filesystem::fileExists($settings_config = PATH['config'] . '/' . 'settings.yaml')) {
            Registry::set('settings', Yaml::parseFile($settings_config));
        } else {
            throw new \RuntimeException("Flextype settings config file does not exist.");
        }
    }

    /**
     * Get the Flextype instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Flextype::$instance)) {
            Flextype::$instance = new self;
        }

        return Flextype::$instance;
    }
}
