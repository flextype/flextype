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
$app->get('/api/registry', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id    = $query['id'];
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.registry.enabled')) {

        // Validate  token
        if (validate_registry_token($token)) {
            $registry_token_file_path = PATH['project'] . '/tokens/registry/' . $token . '/token.yaml';

            // Set  token file
            if ($registry_token_file_data = $flextype['serializer']->decode(Filesystem::read($registry_token_file_path), 'yaml')) {
                if ($registry_token_file_data['state'] === 'disabled' ||
                    ($registry_token_file_data['limit_calls'] !== 0 && $registry_token_file_data['calls'] >= $registry_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
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
                Filesystem::write($registry_token_file_path, $flextype['serializer']->encode(array_replace_recursive($registry_token_file_data, ['calls' => $registry_token_file_data['calls'] + 1]), 'yaml'));

                if ($response_code == 404) {

                    // Return response
                    return $response
                           ->withJson($api_sys_messages['NotFound'], $response_code);
                }

                // Return response
                return $response
                       ->withJson($response_data, $response_code);
            }

            return $response
                   ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
        }

        return $response
               ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
    }

    return $response
           ->withJson($api_sys_messages['AccessTokenInvalid'], 401);
});
