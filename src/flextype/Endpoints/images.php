<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function array_replace_recursive;
use function filesystem;
use function flextype;

/**
 * Fetch image
 *
 * endpoint: GET /api/images
 *
 * Parameters:
 * path - [REQUIRED] - The file path with valid params for image manipulations.
 *
 * Query:
 * token  - [REQUIRED] - Valid token.
 *
 * Returns:
 * Image file
 */
app()->get('/api/images/{path:.+}', function ($path, Request $request, Response $response) use ($apiErrors) {
    
    // Get Query Params
    $query = $request->getQueryParams();

    // Set response 400
    $response400 = function () use ($response, $apiErrors) {
        $response->getBody()->write(serializers()->json()->encode($apiErrors['400']));
        $response->withStatus($apiErrors['400']['http_status_code']);
        $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));
        return $response;
    };

    // Set response 404
    $response404 = function () use ($response, $apiErrors) {
        $response->getBody()->write(serializers()->json()->encode($apiErrors['404']));
        $response->withStatus($apiErrors['404']['http_status_code']);
        $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));
        return $response;
    };

    // Check is images api enabled
    if (! registry()->get('flextype.settings.api.images.enabled')) {
        return $response400();
    }

    // Check is token param exists
    if (! isset($query['token'])) {
        return $response400();
    }

    // Check is token exists
    if (! tokens()->has($query['token'])) {
        return $response400();
    }

    // Fetch token
    $tokenData = tokens()->fetch($query['token']);

    // Check token state and limit_calls
    if ($tokenData['state'] === 'disabled' || 
        ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])) {
        return $response400();
    }

    // Update token calls
    tokens()->update($query['token'], ['calls' => $tokenData['calls'] + 1]);

    // Check is file exists
    if (! filesystem()->file(PATH['project'] . '/uploads/' . $path)->exists()) {
        return $response404();
    }

    // Return image response
    return container()->get('images')->getImageResponse($path, $_GET);
});
