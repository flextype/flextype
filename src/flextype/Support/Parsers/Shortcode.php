<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use Exception;
use Thunder\Shortcode\ShortcodeFacade;

use function flextype;
use function strings;

final class Shortcode
{
    /**
     * The Shortcode's instance is stored in a static field. This field is an
     * array, because we'll allow our Shortcode to have subclasses. Each item in
     * this array will be an instance of a specific Shortcode's subclass.
     *
     * @var array
     */
    private static $instances = [];

     /**
      * Shortcode facade
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
     * Shortcode construct
     *
     * @param
     */
    protected function __construct()
    {
        $this->shortcodeFacade = new ShortcodeFacade();
    }

    /**
     * Shortcode facade
     *
     * @param
     */
    public function facade(): ShortcodeFacade
    {
        return $this->shortcodeFacade;
    }

    /**
     * Returns Shortcode Instance
     *
     * @param
     */
    public static function getInstance(): Shortcode
    {
        $cls = static::class;
        if (! isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * Add shortcode handler.
     *
     * @param string   $name    Shortcode
     * @param callable $handler Handler
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
     * @param string   $name    Event
     * @param callable $handler Handler
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
    public function parse(string $input)
    {
        return $this->facade()->parse($input);
    }

    /**
     * Processes text and replaces shortcodes.
     *
     * @param string $input A text containing SHORTCODE
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @access public
     */
    public function process(string $input, bool $cache = true)
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $this->facade()->process($input);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $this->facade()->process($input);
    }

    /**
     * Get Cache ID for shortcode
     *
     * @param  string $input Input
     *
     * @return string Cache ID
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('shortcode' . $input)->hash()->toString();
    }
}
