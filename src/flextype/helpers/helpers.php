<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Flextype;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Finder\Finder;

if (! function_exists('flextype')) {
    /**
     * Get the available Flextype instance.
     */
    function flextype($container = null)
    {
        return Flextype::getInstance($container);
    }
}

if (! function_exists('app')) {
    /**
     * Get Flextype App.
     */
    function app()
    {
        return flextype()->app();
    }
}

if (! function_exists('container')) {
    /**
     * Get Flextype Container.
     */
    function container()
    {
        return flextype()->container();
    }
}

if (! function_exists('emitter')) {
    /**
     * Get Flextype Emitter Service.
     */
    function emitter()
    {
        return flextype()->container()->get('emitter');
    }
}

if (! function_exists('cache')) {
    /**
     * Get Flextype Cache Service.
     */
    function cache()
    {
        return flextype()->container()->get('cache');
    }
}

if (! function_exists('content')) {
    /**
     * Get Flextype Content Service.
     */
    function content()
    {
        return flextype()->container()->get('content');
    }
}

if (! function_exists('media')) {
    /**
     * Get Flextype Media Service.
     */
    function media()
    {
        return flextype()->container()->get('media');
    }
}

if (! function_exists('parsers')) {
    /**
     * Get Flextype Parsers Service.
     */
    function parsers()
    {
        return flextype()->container()->get('parsers');
    }
}

if (! function_exists('serializers')) {
    /**
     * Get Flextype Serializers Service.
     */
    function serializers()
    {
        return flextype()->container()->get('serializers');
    }
}

if (! function_exists('logger')) {
    /**
     * Get Flextype Logger Service.
     */
    function logger()
    {
        return flextype()->container()->get('logger');
    }
}

if (! function_exists('session')) {
    /**
     * Get Flextype Session Service.
     */
    function session()
    {
        return flextype()->container()->get('session');
    }
}

if (! function_exists('registry')) {
    /**
     * Get Flextype Registry Service.
     */
    function registry()
    {
        return flextype()->container()->get('registry');
    }
}

if (! function_exists('csrf')) {
    /**
     * Get Flextype CSRF Service.
     */
    function csrf()
    {
        return flextype()->container()->get('csrf');
    }
}

if (! function_exists('plugins')) {
    /**
     * Get Flextype Plugins Service.
     */
    function plugins()
    {
        return flextype()->container()->get('plugins');
    }
}

if (! function_exists('tokens')) {
    /**
     * Get Flextype Tokens Service.
     */
    function tokens()
    {
        return flextype()->container()->get('tokens');
    }
}

if (! function_exists('find')) {
    /**
     * Create a Finder instance with predefined filter params or without them.
     *
     * @param  string $path     Path.
     * @param  array  $options  Options array.
     * @param  string $searchIn Search in 'files' or 'directories'. Default is 'files'.
     * 
     * @return Finder Finder instance.
     */
    function find(string $path = '', array $options = [], string $searchIn = 'files'): Finder
    {
        $find = filesystem()->find()->in($path);

        isset($options['depth']) and $find->depth($options['depth']) or $find->depth(1);
        isset($options['date']) and $find->date($options['date']);
        isset($options['size']) and $find->size($options['size']);
        isset($options['exclude']) and $find->exclude($options['exclude']);
        isset($options['contains']) and $find->contains($options['contains']);
        isset($options['not_contains']) and $find->notContains($options['not_contains']);
        isset($options['filter']) and $find->filter($options['filter']);
        isset($options['sort']) and $find->sort($options['sort']);
        isset($options['path']) and $find->path($options['path']);
        isset($options['sort_by']) && $options['sort_by'] === 'atime' and $find->sortByAccessedTime();
        isset($options['sort_by']) && $options['sort_by'] === 'mtime' and $find->sortByModifiedTime();
        isset($options['sort_by']) && $options['sort_by'] === 'ctime' and $find->sortByChangedTime();

        return $searchIn === 'directories' ? $find->directories() : $find->files();
    }
}

if (! function_exists('filterCollection')) {
    /**
     * Filter collection.
     *
     * @param  mixed $items   Items.
     * @param  array $options Options array.
     *
     * @return array
     */
    function filterCollection($items = [], array $options = []): array
    {
        $collection = arrays($items);

        ! isset($options['return']) and $options['return'] = 'all';

        if (isset($options['only'])) {
            $collection->only($options['only']);
        }

        if (isset($options['except'])) {
            $collection->except($options['except']);
        }

        if (isset($options['where'])) {
            if (is_array($options['where'])) {
                foreach ($options['where'] as $key => $value) {
                    if (
                        ! isset($value['key']) ||
                        ! isset($value['operator']) ||
                        ! isset($value['value'])
                    ) {
                        continue;
                    }

                    $collection->where($value['key'], $value['operator'], $value['value']);
                }
            }
        }

        if (isset($options['group_by'])) {
            $collection->groupBy($options['group_by']);
        }

        if (isset($options['sort_by'])) {
            if (isset($options['sort_by']['key']) && isset($options['sort_by']['direction'])) {
                $collection->sortBy($options['sort_by']['key'], $options['sort_by']['direction']);
            }
        }

        if (isset($options['offset'])) {
            $collection->offset(isset($options['offset']) ? (int) $options['offset'] : 0);
        }

        if (isset($options['limit'])) {
            $collection->limit(isset($options['limit']) ? (int) $options['limit'] : 0);
        }

        switch ($options['return']) {
            case 'first':
                $result = $collection->first();
                break;
            case 'last':
                $result = $collection->last();
                break;
            case 'next':
                $result = $collection->next();
                break;
            case 'random':
                $result = $collection->random(isset($options['random']) ? (int) $options['random'] : null);
                break;
            case 'shuffle':
                $result = $collection->shuffle()->toArray();
                break;
            case 'all':
            default:
                $result = $collection->all();
                break;
        }

        return $result;
    }
}

if (! function_exists('images')) {
    /**
     * Get Flextype Images Service.
     */
    function images()
    {
        return flextype()->container()->get('images');
    }
}

if (! function_exists('imageFile')) {
    /**
     * Create a new image instance.
     *
     * @param  string $file    Image file.
     * @param  array  $options Options array.
     *
     * @return Image|void
     */
    function imageFile(string $file, array $options = [])
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
    function imageCanvas(int $width, int $height, $background = null): Image
    {
        return Image::canvas($width, $height, $background);
    }
}

if (! function_exists('imageCache')) {
    /**
     * Create a new cached image instance.
     *
     * @param  Closure $callback   A closure containing the operations on an image, defining the cached image.
     * @param  int     $lifetime   The lifetime in minutes of the image callback in the cache.
     * @param  bool    $returnObj  Decide if you want the method to return an Intervention Image instance or (by default) the image stream.
     *
     * @return mixed Intervention Image instance as return value or just receive the image stream.
     */
    function imageCache(Closure $callback, int $lifetime = 5, bool $returnObj = false)
    {
        return Image::cache($callback, $lifetime, $returnObj);
    }
}
