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
     * Flextype Application
     */
    protected $flextype;

    /**
     * __construct
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Init CORS
     */
    public function init() : void
    {

        $flextype = $this->flextype;

        if (! $flextype->container('registry')->get('flextype.settings.cors.enabled')) {
            return;
        }

        $flextype->options('/{routes:.+}', function ($request, $response) {
            return $response;
        });

        $flextype->add(function ($req, $res, $next) use ($flextype) {
            $response = $next($req, $res);

            // Set variables
            $origin      = $flextype->container('registry')->get('flextype.settings.cors.origin');
            $headers     = count($flextype->container('registry')->get('flextype.settings.cors.headers')) ? implode(', ', $flextype->container('registry')->get('flextype.settings.cors.headers')) : '';
            $methods     = count($flextype->container('registry')->get('flextype.settings.cors.methods')) ? implode(', ', $flextype->container('registry')->get('flextype.settings.cors.methods')) : '';
            $expose      = count($flextype->container('registry')->get('flextype.settings.cors.expose')) ? implode(', ', $flextype->container('registry')->get('flextype.settings.cors.expose')) : '';
            $credentials = $flextype->container('registry')->get('flextype.settings.cors.credentials') ? true : false;

            return $response
                    ->withHeader('Access-Control-Allow-Origin', $origin)
                    ->withHeader('Access-Control-Allow-Headers', $headers)
                    ->withHeader('Access-Control-Allow-Methods', $methods)
                    ->withHeader('Access-Control-Allow-Expose', $expose)
                    ->withHeader('Access-Control-Allow-Credentials', $credentials);
        });
    }
}
