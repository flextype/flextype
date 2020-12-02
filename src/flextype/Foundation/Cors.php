<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Foundation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function count;
use function flextype;
use function implode;

class Cors
{
    /**
     * Init CORS
     */
    public function init(): void
    {
        if (! flextype('registry')->get('flextype.settings.cors.enabled')) {
            return;
        }

        flextype()->options('/{routes:.+}', function (ServerRequestInterface $request, ResponseInterface $response) {
            return $response;
        });

        flextype()->add(function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
            $response = $next($request, $response);

            // Set variables
            $origin      = flextype('registry')->get('flextype.settings.cors.origin');
            $headers     = count(flextype('registry')->get('flextype.settings.cors.headers')) ? implode(', ', flextype('registry')->get('flextype.settings.cors.headers')) : '';
            $methods     = count(flextype('registry')->get('flextype.settings.cors.methods')) ? implode(', ', flextype('registry')->get('flextype.settings.cors.methods')) : '';
            $expose      = count(flextype('registry')->get('flextype.settings.cors.expose')) ? implode(', ', flextype('registry')->get('flextype.settings.cors.expose')) : '';
            $credentials = flextype('registry')->get('flextype.settings.cors.credentials') ? true : false;

            return $response
                    ->withHeader('Access-Control-Allow-Origin', $origin)
                    ->withHeader('Access-Control-Allow-Headers', $headers)
                    ->withHeader('Access-Control-Allow-Methods', $methods)
                    ->withHeader('Access-Control-Allow-Expose', $expose)
                    ->withHeader('Access-Control-Allow-Credentials', $credentials);
        });
    }
}
