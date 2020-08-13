<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\App\Foundation;

use function count;
use function implode;

class Cors
{
    /**
     * Dependency Container
     */
    private $container;

    /**
     * Flextype app
     */
    private $flextype;

    /**
     * __construct
     */
    public function __construct($flextype)
    {
        $this->flextype  = $flextype;
        $this->container = $flextype->getContainer();
    }

    /**
     * Init CORS
     */
    public function init() : void
    {
        $container = $this->container;

        if (! $container['registry']->get('flextype.settings.cors.enabled')) {
            return;
        }

        $this->flextype->options('/{routes:.+}', function ($request, $response) {
            return $response;
        });

        $this->flextype->add(function ($req, $res, $next) use ($container) {
            $response = $next($req, $res);

            // Set variables
            $origin      = $container['registry']->get('flextype.settings.cors.origin');
            $headers     = count($container['registry']->get('flextype.settings.cors.headers')) ? implode(', ', $container['registry']->get('flextype.settings.cors.headers')) : '';
            $methods     = count($container['registry']->get('flextype.settings.cors.methods')) ? implode(', ', $container['registry']->get('flextype.settings.cors.methods')) : '';
            $expose      = count($container['registry']->get('flextype.settings.cors.expose')) ? implode(', ', $container['registry']->get('flextype.settings.cors.expose')) : '';
            $credentials = $container['registry']->get('flextype.settings.cors.credentials') ? true : false;

            return $response
                    ->withHeader('Access-Control-Allow-Origin', $origin)
                    ->withHeader('Access-Control-Allow-Headers', $headers)
                    ->withHeader('Access-Control-Allow-Methods', $methods)
                    ->withHeader('Access-Control-Allow-Expose', $expose)
                    ->withHeader('Access-Control-Allow-Credentials', $credentials);
        });
    }
}
