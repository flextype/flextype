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
flextype()->get('/api/registry', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response->withStatus($api_errors['0300']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0300']));
    }

    // Set variables
    $id    = $query['id'];
    $token = $query['token'];

    if (flextype('registry')->get('flextype.settings.api.registry.enabled')) {
        // Validate  token
        if (validate_registry_token($token)) {
            $registry_token_file_path = PATH['project'] . '/tokens/registry/' . $token . '/token.yaml';

            // Set  token file
            if ($registry_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($registry_token_file_path)->get())) {
                if (
                    $registry_token_file_data['state'] === 'disabled' ||
                    ($registry_token_file_data['limit_calls'] !== 0 && $registry_token_file_data['calls'] >= $registry_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0003']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Fetch registry
                if (flextype('registry')->has($id)) {
                    $response_data['data']['key']   = $id;
                    $response_data['data']['value'] = flextype('registry')->get($id);

                    // Set response code
                    $response_code = 200;
                } else {
                    $response_data = [];
                    $response_code = 404;
                }

                // Update calls counter
                filesystem()->file($registry_token_file_path)
                                      ->put(flextype('serializers')->yaml()->encode(array_replace_recursive($registry_token_file_data, ['calls' => $registry_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withStatus($api_errors['0302']['http_status_code'])
                           ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                           ->write(flextype('serializers')->json()->encode($api_errors['0302']));
                }

                // Return response
                return $response
                       ->withJson($response_data, $response_code);
            }

            return $response
                   ->withStatus($api_errors['0003']['http_status_code'])
                   ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                   ->write(flextype('serializers')->json()->encode($api_errors['0003']));
        }

        return $response
               ->withStatus($api_errors['0003']['http_status_code'])
               ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
               ->write(flextype('serializers')->json()->encode($api_errors['0003']));
    }

    return $response
            ->withStatus($api_errors['0003']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('serializers')->json()->encode($api_errors['0003']));
});
