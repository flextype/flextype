<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support\Parsers;

use Exception;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Table\TableExtension;

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
        $config = flextype('registry')->get('flextype.settings.parsers.markdown');
        $this->environment = Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new AttributesExtension());
        $this->environment->addExtension(new TableExtension());
        $this->converter = new CommonMarkConverter($config, $this->environment);
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
    public function converter(): CommonMarkConverter
    {
        return $this->converter;
    }

    /**
     * Returns Markdown Instance
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
    public function parse(string $input, bool $cache = true)
    {
        if ($cache === true && flextype('registry')->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = flextype('cache')->get($key)) {
                return $dataFromCache;
            }

            $data = $this->converter()->convertToHtml($input);
            flextype('cache')->set($key, $data);

            return $data;
        }

        return $this->converter()->convertToHtml($input);
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
