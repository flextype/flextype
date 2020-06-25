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
 * API sys messages
 */
$api_sys_messages['AccessTokenInvalid'] = ['sys' => ['type' => 'Error', 'id' => 'AccessTokenInvalid'], 'message' => 'The access token you sent could not be found or is invalid.'];
$api_sys_messages['NotFound'] = ['sys' => ['type' => 'Error', 'id' => 'NotFound'], 'message' => 'The resource could not be found.'];

/**
 * Validate delivery files token
 */
function validate_delivery_media_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/delivery/media/' . $token . '/token.yaml');
}

/**
 * Fetch media files collection
 *
 * endpoint: GET /api/delivery/media
 *
 * Query:
 * folder - [REQUIRED] - Unique identifier of the files folder.
 * token  - [REQUIRED] - Valid Content Delivery API token for Entries.
 *
 * Returns:
 * An array of entry item objects.
 */
$app->get('/api/delivery/media/files', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $folder  = $query['folder'];
    $token   = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.delivery.files.enabled')) {

        // Validate delivery token
        if (validate_delivery_files_token($token)) {
            $delivery_files_token_file_path = PATH['project'] . '/tokens/delivery/files/' . $token. '/token.yaml';

            // Set delivery token file
            if ($delivery_files_token_file_data = $flextype['serializer']->decode(Filesystem::read($delivery_files_token_file_path), 'yaml')) {
                if ($delivery_files_token_file_data['state'] === 'disabled' ||
                    ($delivery_files_token_file_data['limit_calls'] !== 0 && $delivery_files_token_file_data['calls'] >= $delivery_files_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Create files array
                $files = [];

                // Get list if files for specific folder
                $files = $flextype['media_files']->fetchCollection($folder);

                // Write response data
                $response_data['data'] = $files;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($delivery_files_token_file_path, $flextype['serializer']->encode(array_replace_recursive($delivery_files_token_file_data, ['calls' => $delivery_files_token_file_data['calls'] + 1]), 'yaml'));

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
