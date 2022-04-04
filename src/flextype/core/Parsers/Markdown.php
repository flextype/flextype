<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Parsers;

use Exception;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

use function cache;
use function registry;
use function strings;

final class Markdown
{
    /**
     * Markdown instance.
     */
    private static ?Markdown $instance = null;

     /**
      * Markdown Environment
      */
    private $environment = null;

    /**
     * Markdown Converter
     */
    private $converter = null;

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
     */
    protected function __construct()
    {
        $this->environment = new Environment(registry()->get('flextype.settings.parsers.markdown.commonmark'));
        $this->environment->addExtension(new CommonMarkCoreExtension());
        $this->environment->addExtension(new AttributesExtension());
        $this->environment->addExtension(new TableExtension());
        $this->converter = new MarkdownConverter($this->environment);
    }

    /**
     * Markdown Environment
     */
    public function environment(): Environment
    {
        return $this->environment;
    }

    /**
     * Markdown Converter
     */
    public function converter(): MarkdownConverter
    {
        return $this->converter;
    }

    /**
     * Gets the instance via lazy initialization (created on first usage).
     */
    public static function getInstance(): Markdown
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Takes a MARKDOWN encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing MARKDOWN
     *
     * @return mixed The MARKDOWN converted to a PHP value
     */
    public function parse(string $input)
    {
        $cache = registry()->get('flextype.settings.parsers.markdown.cache');

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $this->converter()->convertToHtml($input)->getContent();
            cache()->set($key, $data);

            return $data;
        }

        return $this->converter()->convertToHtml($input)->getContent();
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
