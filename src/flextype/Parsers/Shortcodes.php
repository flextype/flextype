<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers;

use Exception;
use Thunder\Shortcode\ShortcodeFacade;

use function flextype;
use function strings;

final class Shortcodes
{
    /**
     * Shortcodes instance.
     */
    private static ?Shortcodes $instance = null;

    /**
     * Shortcode facade.
     */
    private $shortcodeFacade = null;

    /**
     * Shortcode should not be cloneable.
     */
    protected function __clone()
    {
        throw new Exception('Cannot clone a Shortcode.');
    }

    /**
     * Shortcode should not be restorable from strings.
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize a Shortcode.');
    }

    /**
     * Shortcode construct.
     */
    protected function __construct()
    {
        $this->shortcodeFacade = new ShortcodeFacade();
    }

    /**
     * Gets the instance via lazy initialization (created on first usage).
     */
    public static function getInstance(): Shortcodes
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Shortcode facade.
     */
    public function facade(): ShortcodeFacade
    {
        return $this->shortcodeFacade;
    }

    /**
     * Init Shortcodes
     */
    public function initShortcodes(): void
    {
        $shortcodes = registry()->get('flextype.settings.parsers.shortcodes.shortcodes');

        if (
            ! isset($shortcodes) ||
            ! is_array($shortcodes) ||
            count($shortcodes) <= 0
        ) {
            return;
        }

        foreach ($shortcodes as $shortcode) {
            if (! isset($shortcode['path'])) {
                continue;
            }

            if (! file_exists(ROOT_DIR . $shortcode['path'])) {
                
                continue;
            }

            include_once ROOT_DIR . $shortcode['path'];
        }
    }

    /**
     * Add shortcode handler.
     *
     * @param string   $name    Shortcode.
     * @param callable $handler Handler.
     *
     * @access public
     */
    public function addHandler(string $name, callable $handler)
    {
        return $this->facade()->addHandler($name, $handler);
    }

    /**
     * Add event handler.
     *
     * @param string   $name    Event.
     * @param callable $handler Handler.
     *
     * @access public
     */
    public function addEventHandler(string $name, callable $handler)
    {
        return $this->facade()->addEventHandler($name, $handler);
    }

    /**
     * Parses text into shortcodes.
     *
     * @param string $input A text containing SHORTCODE
     *
     * @access public
     */
    public function parseText(string $input)
    {
        return $this->facade()->parse($input);
    }

    /**
     * Parse and processes text to replaces shortcodes.
     *
     * @param string $input A text containing SHORTCODE
     * @param bool   $cache Cache result data or no. Default is true.
     *
     * @access public
     */
    public function parse(string $input)
    {
        $cache = registry()->get('flextype.settings.parsers.shortcodes.cache');

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $this->facade()->process($input);
            cache()->set($key, $data);

            return $data;
        }

        return $this->facade()->process($input);
    }

    /**
     * Get Cache ID for shortcode.
     *
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('shortcode' . $input)->hash()->toString();
    }
}
