<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Validate delivery registry token
 */
function validate_delivery_registry_token($request, $flextype) : bool
{
    return Filesystem::has(PATH['tokens'] . '/delivery/registry/' . $request->getQueryParams()['token'] . '/token.yaml');
}

/**
 * Fetch registry
 *
 * endpoint: /api/delivery/registry
 */
$app->get('/api/delivery/registry', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id = $query['id'];

    if ($flextype['registry']->get('flextype.api.registry.enabled')) {

        // Validate delivery token
        if (validate_delivery_registry_token($request, $flextype)) {
            $delivery_registry_token_file_path = PATH['tokens'] . '/delivery/registry/' . $request->getQueryParams()['token'] . '/token.yaml';

            // Set delivery token file
            if ($delivery_registry_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_registry_token_file_path), 'yaml')) {
                if ($delivery_registry_token_file_data['state'] == 'disabled' ||
                    ($delivery_registry_token_file_data['limit_calls'] != 0 && $delivery_registry_token_file_data['calls'] >= $delivery_registry_token_file_data['limit_calls'])) {
                    return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
                } else {
                    // Fetch registry
                    $data = $flextype['registry']->get($id);

                    // Update calls counter
                    Filesystem::write($delivery_registry_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_registry_token_file_data, ['calls' => $delivery_registry_token_file_data['calls'] + 1]), 'yaml'));

                    // Return response
                    return $response->withJson($data, 200);
                }
            } else {
                return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
            }
        } else {
            return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
        }
    } else {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
    }
});
