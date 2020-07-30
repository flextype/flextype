<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;
use function array_replace_recursive;

/**
 * Validate registry token
 */
function validate_registry_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/registry/' . $token . '/token.yaml');
}

/**
 * Fetch registry item
 *
 * endpoint: GET /api/registry
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the registry item.
 * token  - [REQUIRED] - Valid Registry token.
 *
 * Returns:
 * An array of registry item objects.
 */
$app->get('/api/registry', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response->withJson($api_errors['0300'], $api_errors['0300']['http_status_code']);
    }

    // Set variables
    $id    = $query['id'];
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.registry.enabled')) {
        // Validate  token
        if (validate_registry_token($token)) {
            $registry_token_file_path = PATH['project'] . '/tokens/registry/' . $token . '/token.yaml';

            // Set  token file
            if ($registry_token_file_data = $flextype['yaml']->decode(Filesystem::read($registry_token_file_path))) {
                if ($registry_token_file_data['state'] === 'disabled' ||
                    ($registry_token_file_data['limit_calls'] !== 0 && $registry_token_file_data['calls'] >= $registry_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0003'], $api_errors['0003']['http_status_code']);
                }

                // Fetch registry
                if ($flextype['registry']->has($id)) {
                    $response_data['data']['key']   = $id;
                    $response_data['data']['value'] = $flextype['registry']->get($id);

                    // Set response code
                    $response_code = 200;
                } else {
                    $response_data = [];
                    $response_code = 404;
                }

                // Update calls counter
                Filesystem::write($registry_token_file_path, $flextype['yaml']->encode(array_replace_recursive($registry_token_file_data, ['calls' => $registry_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withJson($api_errors['0302'], $api_errors['0302']['http_status_code']);
                }

                // Return response
                return $response
                       ->withJson($response_data, $response_code);
            }

            return $response
                   ->withJson($api_errors['0003'], $api_errors['0003']['http_status_code']);
        }

        return $response
               ->withJson($api_errors['0003'], $api_errors['0003']['http_status_code']);
    }

    return $response
           ->withJson($api_errors['0003'], $api_errors['0003']['http_status_code']);
});
