<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Arr\Arr;

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
            return Arr::get($this->flextype->serializer->decode(Filesystem::read($config_file), 'yaml'), $key, $default);
        }
    }

    /**
     * Create new config item
     *
     * @param string $config  Config namespace.
     * @param string $key     The key of the config item to get.
     * @param mixed  $value   Value
     *
     * @return bool
     */
    public function create(string $config, string $key, $value) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {

             $config_file_data = $this->flextype->serializer->decode(Filesystem::read($config_file), 'yaml');

             if (!Arr::keyExists($config_file_data, $key)) {
                 Arr::set($config_file_data, $key, $value);
                 return Filesystem::write($config_file, $this->flextype->serializer->encode($config_file_data, 'yaml'));
             }

             return false;
        }

        return false;
    }

    /**
     * Update config item
     *
     * @param string $config  Config namespace.
     * @param string $key     The key of the config item to get.
     * @param mixed  $value   Value
     *
     * @return bool
     */
    public function update(string $config, string $key, $value) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {

             $config_file_data = $this->flextype->serializer->decode(Filesystem::read($config_file), 'yaml');

             if (Arr::keyExists($config_file_data, $key)) {
                 Arr::set($config_file_data, $key, $value);
                 return Filesystem::write($config_file, $this->flextype->serializer->encode($config_file_data, 'yaml'));
             }

             return false;
        }

        return false;
    }

    /**
     * Delete config item
     *
     * @param string $config  Config namespace.
     * @param string $key     The key of the config item to get.
     *
     * @return bool
     */
    public function delete(string $config, $key) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {

             $config_file_data = $this->flextype->serializer->decode(Filesystem::read($config_file), 'yaml');

             if (Arr::keyExists($config_file_data, $key)) {
                 Arr::delete($config_file_data, $key);
                 return Filesystem::write($config_file, $this->flextype->serializer->encode($config_file_data, 'yaml'));
             }

             return false;
        }

        return false;
    }

    /**
     * Checks if an config item with this key name is in the config.
     *
     * @param string $config  Config namespace.
     * @param string $key     The key of the config item to get.
     *
     * @return bool
     */
    public function has(string $config, $key) : bool
    {
        $config_file = $this->getFileLocation($config);

        if (Filesystem::has($config_file)) {

             $config_file_data = $this->flextype->serializer->decode(Filesystem::read($config_file), 'yaml');

             if (Arr::keyExists($config_file_data, $key)) {
                 return true;
             }

             return false;
        }

        return false;
    }

    /**
     * Get config file location
     *
     * @param string $config  Config namespace.
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
     * @param string $config  Config namespace.
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
