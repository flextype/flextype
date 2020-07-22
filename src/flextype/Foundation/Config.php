<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;

class Config
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Get itme from the config
     *
     * @param string $config  Config namespace.
     * @param string $key     The key of the config item to get.
     * @param mixed  $default Default value
     *
     * @return mixed
     */
    public function get(string $config, string $key, $default = null)
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {
            return Arr::get($this->flextype->yaml->decode(Filesystem::read($config_file)), $key, $default);
        }
    }

    /**
     * Create new config item
     *
     * @param string $config Config namespace.
     * @param string $key    The key of the config item to get.
     * @param mixed  $value  Value
     */
    public function create(string $config, string $key, $value) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {
             $config_file_data = $this->flextype->yaml->decode(Filesystem::read($config_file));

            if (! array_has($config_file_data, $key)) {
                Arr::set($config_file_data, $key, $value);

                return Filesystem::write($config_file, $this->flextype->yaml->encode($config_file_data));
            }

             return false;
        }

        return false;
    }

    /**
     * Update config item
     *
     * @param string $config Config namespace.
     * @param string $key    The key of the config item to get.
     * @param mixed  $value  Value
     */
    public function update(string $config, string $key, $value) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {
             $config_file_data = $this->flextype->yaml->decode(Filesystem::read($config_file));

            if (array_has($config_file_data, $key)) {
                Arr::set($config_file_data, $key, $value);

                return Filesystem::write($config_file, $this->flextype->yaml->encode($config_file_data));
            }

             return false;
        }

        return false;
    }

    /**
     * Delete config item
     *
     * @param string $config Config namespace.
     * @param string $key    The key of the config item to get.
     */
    public function delete(string $config, string $key) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {
             $config_file_data = $this->flextype->yaml->decode(Filesystem::read($config_file));

            if (array_has($config_file_data, $key)) {
                array_delete($config_file_data, $key);

                return Filesystem::write($config_file, $this->flextype->yaml->encode($config_file_data));
            }

             return false;
        }

        return false;
    }

    /**
     * Checks if an config item with this key name is in the config.
     *
     * @param string $config Config namespace.
     * @param string $key    The key of the config item to get.
     */
    public function has(string $config, string $key) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {
             $config_file_data = $this->flextype->yaml->decode(Filesystem::read($config_file));

            if (array_has($config_file_data, $key)) {
                return true;
            }

             return false;
        }

        return false;
    }

    /**
     * Get config file location
     *
     * @param string $config Config namespace.
     *
     * @return string config file location
     *
     * @access private
     */
    public function getFileLocation(string $config) : string
    {
        return PATH['project'] . '/config/' . $config . '/settings.yaml';
    }

    /**
     * Get config directory location
     *
     * @param string $config Config namespace.
     *
     * @return string config directory location
     *
     * @access private
     */
    public function getDirLocation(string $config) : string
    {
        return PATH['project'] . '/config/' . $config;
    }
}
