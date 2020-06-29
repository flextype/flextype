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
 * Validate folders token
 */
function validate_folders_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/folders/' . $token . '/token.yaml');
}

/**
 * Fetch folders(s)
 *
 * endpoint: GET /api/folders
 *
 * Query:
 * path   - [REQUIRED] - Folder path.
 * mode   - [REQUIRED] - Mode.
 * token  - [REQUIRED] - Valid Files token.
 *
 * Returns:
 * An array of folder(s) item objects.
 */
$app->get('/api/folders', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $path  = $query['path'];
    $mode  = $query['mode'];
    $token = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {

        // Validate delivery token
        if (validate_folders_token($token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token. '/token.yaml';

            // Set delivery token file
            if ($folders_token_file_data = $flextype['serializer']->decode(Filesystem::read($folders_token_file_path), 'yaml')) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Create folders array
                $folders = [];

                // Get list if folder or fodlers for specific folder
                if ($mode == 'collection') {
                    $folders = $flextype['media_folders']->fetchCollection($path);
                } elseif ($mode == 'single') {
                    $folders = $flextype['media_folders']->fetchSingle($path);
                }

                // Write response data
                $response_data['data'] = $folders;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['serializer']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1]), 'yaml'));

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
