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
 * Validate delivery token
 */
function validate_delivery_token($request, $flextype) : bool
{
    return isset($request->getQueryParams()['delivery_token']) && $request->getQueryParams()['delivery_token'] == $flextype->registry->get('settings.delivery_token') ? true : false;
}

/**
 * Fetch entry(entries)
 *
 * endpoint: /api/entries
 */
$app->get('/api/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id   = $query['id'];
    $args = isset($query['args']) ? $query['args'] : null;

    // Validate delivery token
    if (!validate_delivery_token($request, $flextype)) {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
    }

    // Fetch entry
    $data = $flextype['entries']->fetch($id, $args);

    // Set response code
    $response_code = (count($data) > 0) ? 200 : 404 ;

    // Return response
    return $response->withJson($data, $response_code);
});
