<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Flextype;
use Symfony\Component\Finder\Finder as Finder;


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
    function app() {
        return flextype()->app();
    }
}

if (! function_exists('container')) {
    /**
     * Get Flextype Container.
     */
    function container() {
        return flextype()->container();
    }
}

if (! function_exists('emitter')) {
    /**
     * Get Flextype Emitter Service.
     */
    function emitter() {
        return flextype()->container()->get('emitter');
    }
}

if (! function_exists('cache')) {
    /**
     * Get Flextype Cache Service.
     */
    function cache() {
        return flextype()->container()->get('cache');
    }
}

if (! function_exists('content')) {
    /**
     * Get Flextype Content Service.
     */
    function content() {
        return flextype()->container()->get('content');
    }
}

if (! function_exists('parsers')) {
    /**
     * Get Flextype Parsers Service.
     */
    function parsers() {
        return flextype()->container()->get('parsers');
    }
}

if (! function_exists('serializers')) {
    /**
     * Get Flextype Serializers Service.
     */
    function serializers() {
        return flextype()->container()->get('serializers');
    }
}

if (! function_exists('logger')) {
    /**
     * Get Flextype Logger Service.
     */
    function logger() {
        return flextype()->container()->get('logger');
    }
}

if (! function_exists('session')) {
    /**
     * Get Flextype Session Service.
     */
    function session() {
        return flextype()->container()->get('session');
    }
}

if (! function_exists('csrf')) {
    /**
     * Get Flextype CSRF Service.
     */
    function csrf() {
        return flextype()->container()->get('csrf');
    }
}

if (! function_exists('plugins')) {
    /**
     * Get Flextype Plugins Service.
     */
    function plugins() {
        return flextype()->container()->get('plugins');
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
     * @return Finder
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

if (! function_exists('filter')) {
    /**
     * Create a collection from the given value and filter it.
     *
     * @param  mixed $items   Items.
     * @param  array $options Options array.
     *
     * @return array
     */
    function filter($items = [], array $options = []): array
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
