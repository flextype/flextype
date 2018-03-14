<?php namespace Rawilum;

use Arr;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
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
    protected $config = [];

    /**
     * Constructor.
     *
     * @access  protected
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;

        if ($this->rawilum['filesystem']->exists($site_config = CONFIG_PATH . '/' . 'site.yml')) {
            $this->config['site'] = Yaml::parse(file_get_contents($site_config));
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
        Arr::set($this->config, $key, $value);
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
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Get config array
     *
     * @access  public
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
