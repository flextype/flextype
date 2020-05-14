<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

class Cors
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;


    /**
     * Flextype app
     */
    private $app;

    /**
     * __construct
     */
    public function __construct($flextype, $app)
    {
        $this->flextype = $flextype;
        $this->app      = $app;
    }

    /**
     * Init CORS
     */
    public function init()
    {
        $flextype = $this->flextype;

        if ($flextype['registry']->get('flextype.settings.cors.enabled')) {

            $this->app->options('/{routes:.+}', function ($request, $response, $args) {
                return $response;
            });

            $this->app->add(function ($req, $res, $next) use ($flextype) {
                $response = $next($req, $res);

                // Set variables
                $origin  = $flextype['registry']->get('flextype.settings.cors.origin');
                $headers = count($flextype['registry']->get('flextype.settings.cors.headers')) ? implode(', ', $flextype['registry']->get('flextype.settings.cors.headers')) : '';
                $methods = count($flextype['registry']->get('flextype.settings.cors.methods')) ? implode(', ', $flextype['registry']->get('flextype.settings.cors.methods')) : '';
                $expose  = count($flextype['registry']->get('flextype.settings.cors.expose')) ? implode(', ', $flextype['registry']->get('flextype.settings.cors.expose')) : '';
                $credentials  = ($flextype['registry']->get('flextype.settings.cors.credentials')) ? true : false;

                return $response
                        ->withHeader('Access-Control-Allow-Origin', $origin)
                        ->withHeader('Access-Control-Allow-Headers', $headers)
                        ->withHeader('Access-Control-Allow-Methods', $methods)
                        ->withHeader('Access-Control-Allow-Expose', $expose)
                        ->withHeader('Access-Control-Allow-Credentials', $credentials);
            });
        }
    }
}
