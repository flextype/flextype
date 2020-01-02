<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Validate auth token
 */
function validate_auth_token($request, $flextype) : bool
{
    return isset($request->getQueryParams()['auth_token']) && $request->getQueryParams()['auth_token'] == $flextype->registry->get('settings.auth_token') ? true : false;
}

$app->get('/api/entries', function (Request $request, Response $response, array $args) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Validate auth token
    if (!validate_auth_token($request, $flextype)) {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 404);
    }

    // Response data
    $data = ['s'];

    // Return response
    return $response->withJson($data);
});
