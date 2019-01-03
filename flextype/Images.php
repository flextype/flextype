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

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Event\Event;
use Flextype\Component\Http\Http;


class Images
{
    /**
     * An instance of the Themes class
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Cache Driver
     *
     * @var DoctrineCache
     */
    protected static $server;

    /**
     * Private clone method to enforce singleton behavior.
     *
     * @access private
     */
    private function __clone()
    {
    }

    /**
     * Private wakeup method to enforce singleton behavior.
     *
     * @access private
     */
    private function __wakeup()
    {
    }

    /**
     * Private construct method to enforce singleton behavior.
     *
     * @access private
     */
    private function __construct()
    {
        Images::init();
    }

    /**
     * Init Themes
     *
     * @access private
     * @return void
     */
    private static function init() : void
    {
        // Set source filesystem
        $source = new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local(PATH['entries'])
        );

        // Set cache filesystem
        $cache = new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local(PATH['cache'] . '/glide')
        );

        // Set watermarks filesystem
        $watermarks = new \League\Flysystem\Filesystem(
            new \League\Flysystem\Adapter\Local(PATH['site'] . '/watermarks')
        );

        // Set image manager
        $imageManager = new \Intervention\Image\ImageManager([
            'driver' => 'gd',
        ]);

        // Set manipulators
        $manipulators = [
            new \League\Glide\Manipulators\Orientation(),
            new \League\Glide\Manipulators\Crop(),
            new \League\Glide\Manipulators\Size(2000*2000),
            new \League\Glide\Manipulators\Brightness(),
            new \League\Glide\Manipulators\Contrast(),
            new \League\Glide\Manipulators\Gamma(),
            new \League\Glide\Manipulators\Sharpen(),
            new \League\Glide\Manipulators\Filter(),
            new \League\Glide\Manipulators\Blur(),
            new \League\Glide\Manipulators\Pixelate(),
            new \League\Glide\Manipulators\Watermark($watermarks),
            new \League\Glide\Manipulators\Background(),
            new \League\Glide\Manipulators\Border(),
            new \League\Glide\Manipulators\Encode(),
        ];

        // Set API
        $api = new \League\Glide\Api\Api($imageManager, $manipulators);

        // Setup Glide server
        $server = new \League\Glide\Server(
            $source,
            $cache,
            $api
        );

        Images::$server = $server;
    }

    public static function get($path, $params)
    {
        return Http::getBaseUrl().'/site/cache/glide/'.Images::$server->makeImage($path, $params);
    }

    /**
     * Get the Image instance.
     *
     * @access public
     * @return object
     */
    public static function getInstance()
    {
        if (is_null(Images::$instance)) {
            Images::$instance = new self;
        }

        return Images::$instance;
    }
}
