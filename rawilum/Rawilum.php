<?php
namespace Rawilum;

use Pimple\Container as Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ParsedownExtra;
use Url;

/**
  * Rawilum
  *
  * @package Rawilum
  * @author Romanenko Sergey / Awilum <awilum@msn.com>
  * @link http://rawilum.org
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

class Rawilum extends Container
{
    /**
     * An instance of the Rawilum class
     *
     * @var object
     * @access protected
     */
    protected static $instance;

    /**
     * The version of Rawilum
     *
     * @var string
     */
    const VERSION = 'X.X.X';

    /**
     * Init Rawilum Application
     *
     * @access protected
     */
    protected static function init()
    {
        // Create container
        $container = new static();

        $container['markdown'] = function ($c) {
            return new ParsedownExtra();
        };

        $container['filesystem'] = function ($c) {
            return new Filesystem();
        };

        $container['finder'] = function ($c) {
            return new Finder();
        };

        $container['cache'] = function ($c) {
            return new Cache($c);
        };

        $container['config'] = function ($c) {
            return new Config($c);
        };

        $container['shortcodes'] = function ($c) {
            return new Shortcodes($c);
        };

        $container['events'] = function ($c) {
            return new Events($c);
        };

        $container['filters'] = function ($c) {
            return new Filters($c);
        };

        $container['i18n'] = function ($c) {
            return new I18n($c);
        };

        $container['plugins'] = function ($c) {
            return new Plugins($c);
        };

        $container['pages'] = function ($c) {
            return new Pages($c);
        };

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
    }

    /**
     * Run Rawilum Application
     *
     * @access public
     */
    public function run()
    {
        // Turn on output buffering
        ob_start();

        // Display Errors
        if ($this['config']->get('site.errors.display')) {
            define('DEVELOPMENT', true);
            error_reporting(-1);
        } else {
            define('DEVELOPMENT', false);
            error_reporting(0);
        }

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding($this['config']->get('site.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding($this['config']->get('site.charset'));

        // Set Error handler
        set_error_handler('ErrorHandler::error');
        register_shutdown_function('ErrorHandler::fatal');
        set_exception_handler('ErrorHandler::exception');

        // Set default timezone
        date_default_timezone_set($this['config']->get('site.timezone'));

        // The page is not processed and not sent to the display.
        $this['events']->dispatch('onPageBeforeRender');

        // Render current page
        $this['pages']->renderPage();

        // The page has been fully processed and sent to the display.
        $this['events']->dispatch('onPageAfterRender');

        // Flush (send) the output buffer and turn off output buffering
        ob_end_flush();
    }

    /**
     * Get Rawilum Application Instance
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = static::init();
            RawilumTrait::setRawilum(self::$instance);
        }
        return self::$instance;
    }
}
