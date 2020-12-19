<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use Exception;
use ParsedownExtra;

use function flextype;
use function strings;

final class Markdown
{
    /**
     * The Markdown's instance is stored in a static field. This field is an
     * array, because we'll allow our Markdown to have subclasses. Each item in
     * this array will be an instance of a specific Markdown's subclass.
     *
     * @var array
     */
    private static $instances = [];

     /**
      * Markdown facade
      */
    private $markdownFacade = null;

    /**
     * Markdown should not be cloneable.
     */
    protected function __clone()
    {
        throw new Exception('Cannot clone a Markdown.');
    }

    /**
     * Markdown should not be restorable from strings.
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize a Markdown.');
    }

    /**
     * Markdown construct
     *
     * @param
     */
    protected function __construct()
    {
        $this->markdownFacade = new ParsedownExtra();
        $this->markdownFacade->setBreaksEnabled(flextype('registry')->get('flextype.settings.markdown.auto_line_breaks'));
        $this->markdownFacade->setUrlsLinked(flextype('registry')->get('flextype.settings.markdown.auto_url_links'));
        $this->markdownFacade->setMarkupEscaped(flextype('registry')->get('flextype.settings.markdown.escape_markup'));
    }

    /**
     * Markdown facade
     *
     * @param
     */
    public function facade(): ParsedownExtra
    {
        return $this->markdownFacade;
    }

    /**
     * Returns Markdown Instance
     *
     * @param
     */
    public static function getInstance(): Markdown
    {
        $cls = static::class;
        if (! isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * Takes a MARKDOWN encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing MARKDOWN
     * @param bool   $cache Cache result data or no. Default is true
     *
     * @return mixed The MARKDOWN converted to a PHP value
     */
    public function parse(string $input, bool $cache = true): string
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $this->facade()->text($input);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $this->facade()->text($input);
    }

    /**
     * Get Cache ID for markdown.
     *
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('markdown' . $input)->hash()->toString();
    }
}
