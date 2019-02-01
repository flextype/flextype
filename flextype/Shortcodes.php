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

use Flextype\Component\Event\Event;
use Flextype\Component\Filesystem\Filesystem;
use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Shortcodes {

    /**
     * An instance of the Shortcodes class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Shortcode object
     *
     * @var object
     * @access private
     */
    private static $shortcode = null;

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
        Shortcodes::init();
    }

    /**
     * Init Shortcodes
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        // Create Shortcode Parser object
        Shortcodes::$shortcode = new ShortcodeFacade();

        // Add Default Shorcodes!
        Shortcodes::addDefaultShortcodes();

        // Event: Shortcodes initialized
        Event::dispatch('onShortcodesInitialized');
    }

    /**
     * Returns $shortcode object
     *
     * @access public
     * @return object
     */
    public static function shortcode() : ShortcodeFacade
    {
        return Shortcodes::$shortcode;
    }

    /**
     * Process shortcodes
     *
     * $content = Shortcodes::proccess($content);
     *
     * @access public
     * @param  string $content Content to parse
     * @return string
     */
    public static function process(string $content) : string
    {
        return Shortcodes::shortcode()->process($content);
    }

    /**
     * Add default shortcodes!
     *
     * @access private
     * @return void
     */
    private static function addDefaultShortcodes() : void
    {
        // Get Default Shortocdes List
        $shortcodes_list = Filesystem::listContents(ROOT_DIR . '/flextype/shortcodes');

        // Include default shortcodes
        foreach ($shortcodes_list as $shortcode) {
            include_once $shortcode['path'];
        }
    }

    /**
     * Get the Shortcodes instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Shortcodes::$instance)) {
            Shortcodes::$instance = new self;
        }

        return Shortcodes::$instance;
    }
}
