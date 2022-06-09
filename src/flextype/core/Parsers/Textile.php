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
use Netcarver\Textile\Parser;

use function cache;
use function registry;
use function strings;

final class Textile
{
    /**
     * Markdown instance.
     */
    private static ?Textile $instance = null;

     /**
      * Textile Environment
      */
    private $environment = null;

    /**
     * Textile should not be cloneable.
     */
    protected function __clone()
    {
        throw new Exception('Cannot clone a Textile.');
    }

    /**
     * Textile should not be restorable from strings.
     */
    public function __wakeup(): void
    {
        throw new Exception('Cannot unserialize a Textile.');
    }

    /**
     * Textile construct
     */
    protected function __construct()
    {
        $parser = new Parser();

        foreach (registry()->get('flextype.settings.parsers.textile') as $key => $value) {
            if ($key == 'cache') continue;
            if ($key == 'symbol') {
                if (count($value) > 0 && is_array($value)) {
                    foreach ($value as $name => $val) {
                        $parser->setSymbol($name, $val);
                    }
                }
                continue;
            }
            $parser->{'set' . strings($key)->camel()->ucfirst()}($value);
        }

        $this->environment = $parser;
    }

    /**
     * Gets the instance via lazy initialization (created on first usage).
     */
    public static function getInstance(): Textile
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Textile Environment
     */
    public function environment(): Parser
    {
        return $this->environment;
    }

    /**
     * Takes a TEXTILE encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing TEXTILE
     *
     * @return mixed The TEXTILE converted to a PHP value
     */
    public function parse(string $input)
    {
        $cache = registry()->get('flextype.settings.parsers.textile.cache.enabled');

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $this->environment()->parse($input);
            cache()->set($key, $data);

            return $data;
        }

        return $this->environment()->parse($input);
    }

    /**
     * Get Cache ID for textile.
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
        return strings('textile' . $input . $string . registry()->get('flextype.settings.parsers.textile.cache.string'))->hash()->toString();
    }
}
