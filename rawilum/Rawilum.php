<?php
namespace Rawilum;

use Pimple\Container as Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use ParsedownExtra;

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
    const VERSION = 'X.X.X alfa';

    /**
     * Init Rawilum Application
     */
    protected static function init()
    {
        $container = new static();

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

        $container['events'] = function ($c) {
            return new EventDispatcher();
        };

        $container['filters'] = function ($c) {
            return new Filter($c);
        };

        $container['markdown'] = function ($c) {
            return new ParsedownExtra();
        };

        $container['plugins'] = function ($c) {
            return new Plugins($c);
        };

        $container['plugins']->init();

        $container['pages'] = function ($c) {
          return new Pages($c);
        };

        $container['themes'] = function ($c) {
            return new Themes($c);
        };

        return $container;
    }

    /**
     * Run Rawilum Application
     */
    public function run()
    {
        // Turn on output buffering
        ob_start();

        // Display Errors
        $this['config']->get('site.errors.display') and error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        // Set internal encoding
        function_exists('mb_language') and mb_language('uni');
        function_exists('mb_regex_encoding') and mb_regex_encoding($this['config']->get('site.charset'));
        function_exists('mb_internal_encoding') and mb_internal_encoding($this['config']->get('site.charset'));

        // Set default timezone
        date_default_timezone_set($this['config']->get('site.timezone'));

        // Render page
        $this['pages']->renderPage($this['pages']->getPage(\Url::getUriString()));

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
