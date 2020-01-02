<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/entries', function (Request $request, Response $response, array $args) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // 
    if (!isset($query['auth_token'])) {
        return $response->withJson(["detail" => "Incorrect authentication credentials."]);
    }

    // Response data
    $data = [];

    // Return response
    return $response->withJson($data);
});
