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
use function count;

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
 * path       - [REQUIRED] - Folder path.
 * collection - [OPTIONAL] - Collection or single.
 * token      - [REQUIRED] - Valid Folders token.
 *
 * Returns:
 * An array of folder(s) item objects.
 */
$app->get('/api/folders', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['path']) || ! isset($query['token'])) {
        return $response->withJson($api_errors['0600'], $api_errors['0600']['http_status_code']);
    }

    // Set variables
    $path        = $query['path'];
    $token       = $query['token'];

    if (isset($query['collection'])) {
        if ($query['collection'] == 'true') {
            $collection = true;
        } else {
            $collection = false;
        }
    } else {
        $collection = false;
    }

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {
        // Validate delivery token
        if (validate_folders_token($token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';

            // Set delivery token file
            if ($folders_token_file_data = $flextype['yaml']->decode(Filesystem::read($folders_token_file_path))) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                // Create folders array
                $folders = [];

                // Get list if folder or fodlers for specific folder
                if ($collection) {
                    $folders = $flextype['media_folders']->fetchCollection($path);
                } else {
                    $folders = $flextype['media_folders']->fetchSingle($path);
                }

                // Write response data
                $response_data['data'] = $folders;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['yaml']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withJson($api_errors['0602'], $api_errors['0602']['http_status_code']);
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


/**
 * Create folder
 *
 * endpoint: PUT /api/folders
 *
 * Body:
 * path          - [REQUIRED] - New folder path.
 * token         - [REQUIRED] - Valid Folders token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the folder object for the folder that was just created.
 */
$app->post('/api/folders', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path'])) {
        return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path  = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (($folders_token_file_data = $flextype['yaml']->decode(Filesystem::read($folders_token_file_path))) &&
              ($access_token_file_data = $flextype['yaml']->decode(Filesystem::read($access_token_file_path)))) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                  ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                // Create folder
                $create_folder = $flextype['media_folders']->create($path);

                if ($create_folder) {
                    $response_data['data'] = $flextype['media_folders']->fetch($path);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $create_folder ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['yaml']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withJson($api_errors['0602'], $api_errors['0602']['http_status_code']);
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

/**
 * Copy folder
 *
 * endpoint: PUT /api/folders/copy
 *
 * Body:
 * path            - [REQUIRED] - Unique identifier of the folder.
 * new_path        - [REQUIRED] - New Unique identifier of the folder.
 * token           - [REQUIRED] - Valid Folder token.
 * access_token    - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the folders object for the folders that was just copied.
 */
$app->put('/api/folders/copy', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path  = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (($folders_token_file_data = $flextype['yaml']->decode(Filesystem::read($folders_token_file_path))) &&
              ($access_token_file_data = $flextype['yaml']->decode(Filesystem::read($access_token_file_path)))) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                  ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
                }

                // Copy folder
                $copy_folder = $flextype['media_folders']->copy($path, $new_path);

                if ($copy_folder) {
                    $response_data['data'] = $flextype['media_folders']->fetch($new_path);
                } else {
                    $response_data['data'] = $copy_folder;
                }

                // Set response code
                $response_code = $copy_folder ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['yaml']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withJson($api_errors['0602'], $api_errors['0602']['http_status_code']);
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

/**
 * Rename folder
 *
 * endpoint: PUT /api/folders
 *
 * Body:
 * path            - [REQUIRED] - Unique identifier of the folder.
 * new_pah         - [REQUIRED] - New Unique identifier of the folder.
 * token           - [REQUIRED] - Valid Folder token.
 * access_token    - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the folders object for the folders that was just renamed.
 */
$app->put('/api/folders', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path  = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (($folders_token_file_data = $flextype['yaml']->decode(Filesystem::read($folders_token_file_path))) &&
              ($access_token_file_data = $flextype['yaml']->decode(Filesystem::read($access_token_file_path)))) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                  ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                // Rename folder
                $rename_folder = $flextype['media_folders']->rename($path, $new_path);

                if ($rename_folder) {
                    $response_data['data'] = $flextype['media_folders']->fetch($new_path);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $rename_folder ? 200 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['yaml']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withJson($api_errors['0602'], $api_errors['0602']['http_status_code']);
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

/**
* Delete folder
*
* endpoint: DELETE /api/folders
*
* Body:
* id           - [REQUIRED] - Unique identifier of the folder.
* token        - [REQUIRED] - Valid Folders token.
* access_token - [REQUIRED] - Valid Authentication token.
*
* Returns:
* Returns an empty body with HTTP status 204
*/
$app->delete('/api/folders', function (Request $request, Response $response) use ($flextype, $api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path'])) {
        return $response->withJson($api_errors['0601'], $api_errors['0601']['http_status_code']);
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];

    if ($flextype['registry']->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path  = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (($folders_token_file_data = $flextype['yaml']->decode(Filesystem::read($folders_token_file_path))) &&
              ($access_token_file_data = $flextype['yaml']->decode(Filesystem::read($access_token_file_path)))) {
                if ($folders_token_file_data['state'] === 'disabled' ||
                  ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                  ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_errors['0501'], $api_errors['0501']['http_status_code']);
                }

                // Delete folder
                $delete_folder = $flextype['media_folders']->delete($path);

                // Set response code
                $response_code = $delete_folder ? 204 : 404;

                // Update calls counter
                Filesystem::write($folders_token_file_path, $flextype['yaml']->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withJson($api_errors['0602'], $api_errors['0602']['http_status_code']);
                }

                // Return response
                return $response
                     ->withJson($delete_folder, $response_code);
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
