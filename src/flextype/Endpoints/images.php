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
use function header;

/**
 * Validate images token
 */
function validate_images_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/images/' . $token . '/token.yaml');
}

/**
 * Fetch image
 *
 * endpoint: GET /api/images
 *
 * Parameters:
 * path - [REQUIRED] - The file path with valid params for image manipulations.
 *
 * Query:
 * token  - [REQUIRED] - Valid Images API token.
 *
 * Returns:
 * Image file
 */
$app->get('/api/images/{path:.+}', function (Request $request, Response $response, $args) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.images.enabled')) {

        // Validate delivery image token
        if (validate_images_token($token)) {
            $delivery_images_token_file_path = PATH['project'] . '/tokens/images/' . $token . '/token.yaml';

            // Set delivery token file
            if ($delivery_images_token_file_data = $flextype['serializer']->decode(Filesystem::read($delivery_images_token_file_path), 'yaml')) {

                if ($delivery_images_token_file_data['state'] === 'disabled' ||
                    ($delivery_images_token_file_data['limit_calls'] !== 0 && $delivery_images_token_file_data['calls'] >= $delivery_images_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Update calls counter
                Filesystem::write($delivery_images_token_file_path, $flextype['serializer']->encode(array_replace_recursive($delivery_images_token_file_data, ['calls' => $delivery_images_token_file_data['calls'] + 1]), 'yaml'));

                if (Filesystem::has(PATH['project'] . '/uploads/entries/' . $args['path'])) {
                    header('Access-Control-Allow-Origin: *');

                    return $flextype['images']->getImageResponse($args['path'], $_GET);
                }

                return $response
                    ->withJson($api_sys_messages['NotFound'], 404);
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
