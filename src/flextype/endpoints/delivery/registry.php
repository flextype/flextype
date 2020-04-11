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
use function array_replace_recursive;

/**
 * Validate delivery registry token
 */
function validate_delivery_registry_token($token) : bool
{
    return Filesystem::has(PATH['site'] . '/tokens/delivery/registry/' . $token . '/token.yaml');
}

/**
 * Fetch registry item
 *
 * endpoint: GET /api/delivery/registry
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the registry item.
 * token  - [REQUIRED] - Valid Content Delivery API token for Entries.
 *
 * Returns:
 * An array of registry item objects.
 */
$app->get('/api/delivery/registry', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id    = $query['id'];
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.registry.enabled')) {

        // Validate delivery token
        if (validate_delivery_registry_token($token)) {
            $delivery_registry_token_file_path = PATH['site'] . '/tokens/delivery/registry/' . $token . '/token.yaml';

            // Set delivery token file
            if ($delivery_registry_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_registry_token_file_path), 'yaml')) {
                if ($delivery_registry_token_file_data['state'] === 'disabled' ||
                    ($delivery_registry_token_file_data['limit_calls'] !== 0 && $delivery_registry_token_file_data['calls'] >= $delivery_registry_token_file_data['limit_calls'])) {
                    return $response->withJson(['detail' => 'Incorrect authentication credentials.'], 401);
                }

                // Fetch registry
                if ($flextype['registry']->has($id)) {
                    $data['data']['key']   = $id;
                    $data['data']['value'] = $flextype['registry']->get($id);
                } else {
                    $data = [];
                }

                // Update calls counter
                Filesystem::write($delivery_registry_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_registry_token_file_data, ['calls' => $delivery_registry_token_file_data['calls'] + 1]), 'yaml'));

                // Return response
                return $response
                       ->withJson($data, 200)
                       ->withHeader('Access-Control-Allow-Origin', '*');
            }

            return $response
                   ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
                   ->withHeader('Access-Control-Allow-Origin', '*');
        }

        return $response
               ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
               ->withHeader('Access-Control-Allow-Origin', '*');
    }

    return $response
           ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
           ->withHeader('Access-Control-Allow-Origin', '*');
});
