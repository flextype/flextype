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
 * token - [REQUIRED] - Valid public token.
 *
 * Returns:
 * Image file
 */
app()->get('/api/images/{path:.+}', function ($path, Request $request, Response $response) {
    
    // Get Query Params
    $queryParams = $request->getQueryParams();

    // Check is images api enabled
    if (! registry()->get('flextype.settings.api.images.enabled')) {
        return getApiResponseWithError($response, 400);
    }

    // Check is token param exists
    if (! isset($queryParams['token'])) {
        return getApiResponseWithError($response, 400);
    }

    // Check is token exists
    if (! tokens()->has($queryParams['token'])) {
        return getApiResponseWithError($response, 401);
    }

    // Fetch token
    $tokenData = tokens()->fetch($queryParams['token']);

    // Check token state and limit_calls
    if ($tokenData['state'] === 'disabled' || 
        ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])) {
        return getApiResponseWithError($response, 400);
    }

    // Update token calls
    tokens()->update($queryParams['token'], ['calls' => $tokenData['calls'] + 1]);

    // Check is file exists
    if (! filesystem()->file(PATH['project'] . '/uploads/' . $path)->exists()) {
        return getApiResponseWithError($response, 404);
    }

    // Return image response
    return container()->get('images')->getImageResponse($path, $queryParams);
});
