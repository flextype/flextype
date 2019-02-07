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

use Flextype\Component\Arr\Arr;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Registry\Registry;

class Site
{
    /**
     * An instance of the Site class
     *
     * @var object
     * @access private
     */
    private static $instance = null;

    /**
     * Current site entry data array
     *
     * @var array
     * @access private
     */
    private static $entry = [];

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
        Site::init();
    }

    /**
     * Init Entry
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        Site::processCurrentPage();
    }

    /**
     * Process Current Page
     *
     * @access private
     * @return void
     */
    private static function processCurrentPage() : void
    {
        // Event: The entry is not processed and not sent to the display.
        Event::dispatch('onCurrentEntryBeforeProcessed');

        // Set current requested entry data to global $entry array
        Site::$entry = Entries::getEntry(Http::getUriString());

        // Event: The entry has been fully processed and not sent to the display.
        Event::dispatch('onCurrentEntryBeforeDisplayed');

        // Display entry for current requested url
        Site::displayCurrentPage();

        // Event: The entry has been fully processed and sent to the display.
        Event::dispatch('onCurrentEntryAfterProcessed');
    }

    /**
     * Get current entry
     *
     * $entry = Site::getCurrentEntry();
     *
     * @access  public
     * @return  array
     */
    public static function getCurrentEntry() : array
    {
        return Site::$entry;
    }

    /**
     * Update current entry
     *
     * Site::updateCurrentEntry(['title' => "New Title"]);
     *
     * @access  public
     * @param   array $data  Data
     * @return  void
     */
    public static function updateCurrentEntry(array $data) : void
    {
        Site::$entry = $data;
    }

    /**
     * Update current entry field
     *
     * Site::updateCurrentEntryField('title', "New Title");
     *
     * @access  public
     * @param   string $path  Array path
     * @param   mixed  $value Value to set
     * @return  void
     */
    public static function updateCurrentEntryField(string $path, $value) : void
    {
        Arr::set(Site::$entry, $path, $value);
    }

    /**
     * Display Current Page
     *
     * @access private
     * @return void
     */
    private static function displayCurrentPage() : void
    {
        Http::setRequestHeaders('Content-Type: text/html; charset=' . Registry::get('settings.charset'));
        Themes::view(empty(Site::$entry['template']) ? 'templates/default' : 'templates/' . Site::$entry['template'])
            ->assign('entry', Site::$entry, true)
            ->display();
    }

    /**
     * Get the Content instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Site::$instance)) {
            Site::$instance = new self;
        }

        return Site::$instance;
    }
}
