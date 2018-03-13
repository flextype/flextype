<?php
namespace Rawilum;

use Arr;
use Symfony\Component\Yaml\Yaml;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Config
{
    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * Config
     *
     * @var array
     * @access  protected
     */
    protected static $config = [];

    /**
     * Constructor.
     *
     * @access  protected
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;

        if ($this->rawilum['filesystem']->exists($site_config = CONFIG_PATH . '/' . 'site.yml')) {
            self::$config['site'] = Yaml::parse(file_get_contents($site_config));
        } else {
            throw new RuntimeException("Rawilum site config file does not exist.");
        }
    }

    /**
     * Set new or update existing config variable
     *
     * @access public
     * @param string $key   Key
     * @param mixed  $value Value
     */
    public function set($key, $value)
    {
        Arr::set(self::$config, $key, $value);
    }

    /**
     * Get config variable
     *
     * @access  public
     * @param  string $key Key
     * @param  mixed  $default Default value
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get(self::$config, $key, $default);
    }

    /**
     * Get config array
     *
     * @access  public
     * @return array
     */
    public function getConfig()
    {
        return self::$config;
    }
}
