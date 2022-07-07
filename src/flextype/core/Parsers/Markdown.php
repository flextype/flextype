<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Parsers;

use Exception;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

use function Flextype\cache;
use function Flextype\registry;
use function Glowy\Strings\strings;

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
    public function parse(string $input): mixed
    {
        $cache = registry()->get('flextype.settings.parsers.markdown.cache.enabled');

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
     * @param  string $input  Input.
     * @param  string $string String to append to the Cache ID.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input, string $string = ''): string
    {
        return strings('markdown' . $input . $string . registry()->get('flextype.settings.parsers.markdown.cache.string'))->hash()->toString();
    }
}
