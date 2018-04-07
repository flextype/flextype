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
use Symfony\Component\Yaml\Yaml;

class Config
{

    /**
     * An instance of the Config class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Config
     *
     * @var array
     * @access  protected
     */
    protected static $config = [];

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
    }

    /**
     * Init Config
     *
     * @access protected
     * @return void
     */
    protected static function init() : void
    {
        if (Flextype::filesystem()->exists($site_config = CONFIG_PATH . '/' . 'site.yml')) {
            static::$config['site'] = Yaml::parse(file_get_contents($site_config));
        } else {
            throw new RuntimeException("Flextype site config file does not exist.");
        }
    }

    /**
     * Set new or update existing config variable
     *
     * @access public
     * @param string $key   Key
     * @param mixed  $value Value
     */
    public static function set($key, $value) : void
    {
        Arr::set(static::$config, $key, $value);
    }

    /**
     * Get config variable
     *
     * @access  public
     * @param  string $key Key
     * @param  mixed  $default Default value
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Arr::get(static::$config, $key, $default);
    }

    /**
     * Get config array
     *
     * @access  public
     * @return array
     */
    public static function getConfig() : array
    {
        return static::$config;
    }

    /**
     * Return the Config instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Config();
    }
}
