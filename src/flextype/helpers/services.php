<?php 

declare(strict_types=1);

use Flextype\Flextype;

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

if (! function_exists('entries')) {
    /**
     * Get Flextype Entries Service.
     */
    function entries()
    {
        return flextype()->container()->get('entries');
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

if (! function_exists('actions')) {
    /**
     * Get Flextype Actions Service.
     */
    function actions()
    {
        return flextype()->container()->get('actions');
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

if (! function_exists('slugify')) {
    /**
     * Get Flextype Slugify Service.
     */
    function slugify()
    {
        return flextype()->container()->get('slugify');
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