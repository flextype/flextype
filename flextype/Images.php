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

use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Html\Html;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

// Event: onShortcodesInitialized
Event::addListener('onShortcodesInitialized', function () {

    // Shortcode: [image path="home/image.jpg"]
    // Result: Display image
    Entries::shortcode()->addHandler('image', function(ShortcodeInterface $s) {
        $params     = [];
        $attributes = [];

        // API
        // http://glide.thephpleague.com/1.0/api/quick-reference/
        ($s->getParameter('or')) and $params['or'] = $s->getParameter('or');
        ($s->getParameter('flip')) and $params['flip'] = $s->getParameter('flip');
        ($s->getParameter('crop')) and $params['crop'] = $s->getParameter('crop');
        ($s->getParameter('w')) and $params['w'] = $s->getParameter('w');
        ($s->getParameter('h')) and $params['h'] = $s->getParameter('h');
        ($s->getParameter('fit')) and $params['fit'] = $s->getParameter('fit');
        ($s->getParameter('dpr')) and $params['dpr'] = $s->getParameter('dpr');
        ($s->getParameter('bri')) and $params['bri'] = $s->getParameter('bri');
        ($s->getParameter('con')) and $params['con'] = $s->getParameter('con');
        ($s->getParameter('gam')) and $params['gam'] = $s->getParameter('gam');
        ($s->getParameter('sharp')) and $params['sharp'] = $s->getParameter('sharp');
        ($s->getParameter('blur')) and $params['blur'] = $s->getParameter('blur');
        ($s->getParameter('pixel')) and $params['pixel'] = $s->getParameter('pixel');
        ($s->getParameter('filt')) and $params['filt'] = $s->getParameter('filt');
        ($s->getParameter('mark')) and $params['mark'] = $s->getParameter('mark');
        ($s->getParameter('markw')) and $params['markw'] = $s->getParameter('markw');
        ($s->getParameter('markh')) and $params['markh'] = $s->getParameter('markh');
        ($s->getParameter('markx')) and $params['markx'] = $s->getParameter('markx');
        ($s->getParameter('marky')) and $params['marky'] = $s->getParameter('marky');
        ($s->getParameter('markpad')) and $params['markpad'] = $s->getParameter('markpad');
        ($s->getParameter('markpos')) and $params['markpos'] = $s->getParameter('markpos');
        ($s->getParameter('markalpha')) and $params['markalpha'] = $s->getParameter('markalpha');
        ($s->getParameter('bg')) and $params['bg'] = $s->getParameter('bg');
        ($s->getParameter('border')) and $params['border'] = $s->getParameter('border');
        ($s->getParameter('q')) and $params['q'] = $s->getParameter('q');
        ($s->getParameter('fm')) and $params['fm'] = $s->getParameter('fm');

        ($s->getParameter('width'))  and $attributes['width']  = $s->getParameter('width');
        ($s->getParameter('height')) and $attributes['height'] = $s->getParameter('height');
        ($s->getParameter('class'))  and $attributes['class']  = $s->getParameter('class');
        ($s->getParameter('id'))     and $attributes['id']     = $s->getParameter('id');
        ($s->getParameter('alt'))    and $attributes['alt']    = $s->getParameter('alt');

        return Images::getImage($s->getParameter('path'), $params, $attributes);
    });

    // Shortcode: [image_url path="home/image.jpg"]
    // Result: Display image url
    Entries::shortcode()->addHandler('image_url', function(ShortcodeInterface $s) {
        $params = [];

        // API
        // http://glide.thephpleague.com/1.0/api/quick-reference/
        ($s->getParameter('or')) and $params['or'] = $s->getParameter('or');
        ($s->getParameter('flip')) and $params['flip'] = $s->getParameter('flip');
        ($s->getParameter('crop')) and $params['crop'] = $s->getParameter('crop');
        ($s->getParameter('w')) and $params['w'] = $s->getParameter('w');
        ($s->getParameter('h')) and $params['h'] = $s->getParameter('h');
        ($s->getParameter('fit')) and $params['fit'] = $s->getParameter('fit');
        ($s->getParameter('dpr')) and $params['dpr'] = $s->getParameter('dpr');
        ($s->getParameter('bri')) and $params['bri'] = $s->getParameter('bri');
        ($s->getParameter('con')) and $params['con'] = $s->getParameter('con');
        ($s->getParameter('gam')) and $params['gam'] = $s->getParameter('gam');
        ($s->getParameter('sharp')) and $params['sharp'] = $s->getParameter('sharp');
        ($s->getParameter('blur')) and $params['blur'] = $s->getParameter('blur');
        ($s->getParameter('pixel')) and $params['pixel'] = $s->getParameter('pixel');
        ($s->getParameter('filt')) and $params['filt'] = $s->getParameter('filt');
        ($s->getParameter('mark')) and $params['mark'] = $s->getParameter('mark');
        ($s->getParameter('markw')) and $params['markw'] = $s->getParameter('markw');
        ($s->getParameter('markh')) and $params['markh'] = $s->getParameter('markh');
        ($s->getParameter('markx')) and $params['markx'] = $s->getParameter('markx');
        ($s->getParameter('marky')) and $params['marky'] = $s->getParameter('marky');
        ($s->getParameter('markpad')) and $params['markpad'] = $s->getParameter('markpad');
        ($s->getParameter('markpos')) and $params['markpos'] = $s->getParameter('markpos');
        ($s->getParameter('markalpha')) and $params['markalpha'] = $s->getParameter('markalpha');
        ($s->getParameter('bg')) and $params['bg'] = $s->getParameter('bg');
        ($s->getParameter('border')) and $params['border'] = $s->getParameter('border');
        ($s->getParameter('q')) and $params['q'] = $s->getParameter('q');
        ($s->getParameter('fm')) and $params['fm'] = $s->getParameter('fm');

        return Images::getImageUrl($s->getParameter('path'), $params);
    });
});

class Images
{
    /**
     * An instance of the Themes class
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Images Server
     *
     * @var
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
     * Init Images
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

    /**
     * Get image url
     *
     * Images::getImageUrl('page-name/image.jpg', [w => '200']);
     * http://glide.thephpleague.com/1.0/api/quick-reference/
     *
     * @access public
     * @param  string  $path    Image path
     * @param  array   $params  Image params
     * @return string
     */
    public static function getImageUrl($path, array $params)
    {
        if (file_exists(PATH['entries'] . '/' . $path)) {
            return Http::getBaseUrl() . '/site/cache/glide/' . Images::$server->makeImage($path, $params);
        } else {
            return "File {$path} does not exist.";
        }
    }

    /**
     * Get image
     *
     * Images::getImage('page-name/image.jpg', [w => '200']);
     * http://glide.thephpleague.com/1.0/api/quick-reference/
     *
     * @access public
     * @param  string  $path        Image path
     * @param  array   $params      Image params
     * @param  array   $attributes  Image html attributes
     * @return string
     */
    public static function getImage($path, array $params, array $attributes = [])
    {
        if (file_exists(PATH['entries'] . '/' .  $path)) {
            return '<img '.Html::attributes($attributes).' src="'. Images::getImageUrl($path, $params) .'">';
        } else {
            return "File {$path} does not exist.";
        }
    }

    /**
     * Returns server variable
     *
     * @access public
     * @return object
     */
    public static function server()
    {
        return Images::$server;
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
