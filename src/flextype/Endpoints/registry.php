<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

use function array_replace_recursive;
use function filesystem;
use function flextype;

/**
 * Validate registry token
 */
function validate_registry_token(string $token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/registry/' . $token . '/token.yaml')->exists();
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
app()->get('/api/registry', function (Request $request, Response $response) use ($apiErrors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response->withStatus($apiErrors['0300']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                        ->write(serializers()->json()->encode($apiErrors['0300']));
    }

    // Set variables
    $id    = $query['id'];
    $token = $query['token'];

    if (registry()->get('flextype.settings.api.registry.enabled')) {
        // Validate  token
        if (validate_registry_token($token)) {
            $registry_token_file_path = PATH['project'] . '/tokens/registry/' . $token . '/token.yaml';

            // Set  token file
            if ($registry_token_file_data = serializers()->yaml()->decode(filesystem()->file($registry_token_file_path)->get())) {
                if (
                    $registry_token_file_data['state'] === 'disabled' ||
                    ($registry_token_file_data['limit_calls'] !== 0 && $registry_token_file_data['calls'] >= $registry_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($apiErrors['0003']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                    ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Fetch registry
                if (registry()->has($id)) {
                    $response_data['data']['key']   = $id;
                    $response_data['data']['value'] = registry()->get($id);

                    // Set response code
                    $response_code = 200;
                } else {
                    $response_data = [];
                    $response_code = 404;
                }

                // Update calls counter
                filesystem()->file($registry_token_file_path)
                                      ->put(serializers()->yaml()->encode(array_replace_recursive($registry_token_file_data, ['calls' => $registry_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withStatus($apiErrors['0302']['http_status_code'])
                           ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                           ->write(serializers()->json()->encode($apiErrors['0302']));
                }

                // Return response
                return $response
                       ->withJson($response_data, $response_code);
            }

            return $response
                   ->withStatus($apiErrors['0003']['http_status_code'])
                   ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                   ->write(serializers()->json()->encode($apiErrors['0003']));
        }

        return $response
               ->withStatus($apiErrors['0003']['http_status_code'])
               ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
               ->write(serializers()->json()->encode($apiErrors['0003']));
    }

    return $response
            ->withStatus($apiErrors['0003']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
            ->write(serializers()->json()->encode($apiErrors['0003']));
});
