<?php

declare(strict_types=1);

namespace Flextype;

use Closure;
use Intervention\Image\ImageManagerStatic as Image;

use function count;
use function function_exists;
use function in_array;
use function is_array;

if (! function_exists('imageFile')) {
    /**
     * Create a new image instance for image file.
     *
     * @param  string $file    Image file.
     */
    function imageFile(string $file): Image
    {
        return Image::make($file);
    }
}

if (! function_exists('imageProcessFile')) {
    /**
     * Process image file.
     *
     * @param  string $file    Image file.
     * @param  array  $options Options array.
     */
    function imageProcessFile(string $file, array $options = [])
    {
        $image = Image::make($file);

        if (count($options) === 0) {
            return $image;
        }

        if (isset($options['driver'])) {
            if (in_array($options['driver'], ['imagick', 'gd'])) {
                Image::configure(['driver' => $options['driver']]);
            }
        }

        if (isset($options['blur'])) {
            $image->blur($options['blur']);
        }

        if (isset($options['brightness'])) {
            $image->brightness($options['brightness']);
        }

        if (
            isset($options['colorize']) &&
            isset($options['colorize']['red']) &&
            isset($options['colorize']['green']) &&
            isset($options['colorize']['blue'])
        ) {
            $image->colorize(
                $options['colorize']['red'],
                $options['colorize']['green'],
                $options['colorize']['blue']
            );
        }

        if (isset($options['contrast'])) {
            $image->contrast($options['contrast']);
        }

        if (isset($options['flip'])) {
            $image->flip($options['flip']);
        }

        if (isset($options['gamma'])) {
            $image->gamma($options['gamma']);
        }

        if (isset($options['rotate'])) {
            $image->rotate($options['rotate']);
        }

        if (isset($options['pixelate'])) {
            $image->pixelate($options['pixelate']);
        }

        if (isset($options['heighten'])) {
            $image->heighten($options['heighten']['height'],
                            function ($constraint) use ($options) {
                                if (isset($options['heighten']['constraint']) &&
                                    is_array($options['heighten']['constraint'])) {
                                        foreach ($options['heighten']['constraint'] as $method) {
                                            if (in_array($method, ['upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        if (isset($options['widen'])) {
            $image->heighten($options['widen']['width'], 
                            function ($constraint) use ($options) {
                                if (isset($options['widen']['constraint']) &&
                                    is_array($options['widen']['constraint'])) {
                                        foreach ($options['widen']['constraint'] as $method) {
                                            if (in_array($method, ['upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        if (isset($options['fit']) &&
            isset($options['fit']['width'])) {
            $image->fit($options['fit']['width'], 
                        $options['fit']['height'] ?? null,
                        function ($constraint) use ($options) {
                            if (isset($options['fit']['constraint']) &&
                                is_array($options['fit']['constraint'])) {
                                    foreach ($options['fit']['constraint'] as $method) {
                                        if (in_array($method, ['upsize'])) {
                                            $constraint->{$method}();
                                        }
                                    }
                            }
                        },
                        $options['fit']['position'] ?? 'center');
        }

        if (isset($options['crop']) &&
            isset($options['crop']['width']) &&
            isset($options['crop']['height'])) {
            $image->crop($options['crop']['width'], 
                                $options['crop']['height'],
                                $options['crop']['x'] ?? null,
                                $options['crop']['y'] ?? null);
        }

        
        if (isset($options['invert']) &&
            $options['invert'] == true) {
            $image->invert();
        }

        if (isset($options['sharpen'])) {
            $image->sharpen($options['sharpen']);
        }

        if (isset($options['resize'])) {
            $image->resize($options['resize']['width'] ?? null,
                            $options['resize']['height'] ?? null,
                            function ($constraint) use ($options) {
                                if (isset($options['resize']['constraint']) &&
                                    is_array($options['resize']['constraint'])) {
                                        foreach ($options['resize']['constraint'] as $method) {
                                            if (in_array($method, ['aspectRatio', 'upsize'])) {
                                                $constraint->{$method}();
                                            }
                                        }
                                }
                            });
        }

        $image->save($file, $options['quality'] ?? 70);
        $image->destroy();
    }
}

if (! function_exists('imageCanvas')) {
    /**
     * Create a new image canvas instance.
     *
     * @param  int   $width      Canvas width.
     * @param  int   $height     Canvas height.
     * @param  mixed $background Canvas background.
     *
     * @return Image Image canvas instance.
     */
    function imageCanvas(int $width, int $height, mixed $background = null): Image
    {
        return Image::canvas($width, $height, $background);
    }
}

if (! function_exists('imageCache')) {
    /**
     * Create a new cached image instance.
     *
     * @param  Closure $callback  A closure containing the operations on an image, defining the cached image.
     * @param  int     $lifetime  The lifetime in minutes of the image callback in the cache.
     * @param  bool    $returnObj Decide if you want the method to return an Intervention Image instance or (by default) the image stream.
     *
     * @return mixed Intervention Image instance as return value or just receive the image stream.
     */
    function imageCache(Closure $callback, int $lifetime = 5, bool $returnObj = false): mixed
    {
        return Image::cache($callback, $lifetime, $returnObj);
    }
}
