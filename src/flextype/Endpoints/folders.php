<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

use function array_replace_recursive;
use function count;
use function filesystem;
use function flextype;

/**
 * Validate folders token
 */
function validate_folders_token($token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/folders/' . $token . '/token.yaml')->exists();
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
flextype()->get('/api/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['path']) || ! isset($query['token'])) {
        return $response->withStatus($api_errors['0600']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0600']));
    }

    // Set variables
    $path  = $query['path'];
    $token = $query['token'];

    if (isset($query['collection'])) {
        if ($query['collection'] === 'true') {
            $collection = true;
        } else {
            $collection = false;
        }
    } else {
        $collection = false;
    }

    if (flextype('registry')->get('flextype.settings.api.folders.enabled')) {
        // Validate delivery token
        if (validate_folders_token($token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';

            // Set delivery token file
            if ($folders_token_file_data = flextype('yaml')->decode(filesystem()->file($folders_token_file_path)->get())) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                // Create folders array
                $folders = [];

                // Get list if folder or fodlers for specific folder
                if ($collection) {
                    $folders = flextype('media_folders')->fetchCollection($path)->toArray();
                } else {
                    $folders = flextype('media_folders')->fetchSingle($path)->toArray();
                }

                // Write response data
                $response_data         = [];
                $response_data['data'] = $folders;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withStatus($api_errors['0602']['http_status_code'])
                           ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                           ->write(flextype('json')->encode($api_errors['0602']));
                }

                // Return response
                return $response
                       ->withStatus($response_code)
                       ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                       ->write(flextype('json')->encode($response_data));
            }

            return $response
                   ->withStatus($api_errors['0003']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0003']));
        }

        return $response
               ->withStatus($api_errors['0003']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0003']));
    }

    return $response
           ->withStatus($api_errors['0003']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0003']));
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
flextype()->post('/api/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];

    if (flextype('registry')->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('yaml')->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                // Create folder
                $create_folder = flextype('media_folders')->create($path);

                $response_data = [];

                if ($create_folder) {
                    $response_data['data'] = flextype('media_folders')->fetchSingle($path);
                }

                // Set response code
                $response_code = $create_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withStatus($api_errors['0602']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                     ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                     ->write(flextype('json')->encode($response_data));
            }

            return $response
                        ->withStatus($api_errors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0003']));
        }

        return $response
                    ->withStatus($api_errors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0003']));
    }

    return $response
                ->withStatus($api_errors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($api_errors['0003']));
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
flextype()->put('/api/folders/copy', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if (flextype('registry')->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('yaml')->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0601']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0601']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0601']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0601']));
                }

                // Copy folder
                $copy_folder = flextype('media_folders')->copy($path, $new_path);

                $response_data = [];

                if ($copy_folder) {
                    $response_data['data'] = flextype('media_folders')->fetchSingle($new_path);
                } else {
                    $response_data['data'] = $copy_folder;
                }

                // Set response code
                $response_code = $copy_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($response_data));
            }

            return $response
                        ->withStatus($api_errors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0003']));
        }

        return $response
                    ->withStatus($api_errors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0003']));
    }

    return $response
                ->withStatus($api_errors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($api_errors['0003']));
});

/**
 * Move folder
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
flextype()->put('/api/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if (flextype('registry')->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('yaml')->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                // Rename folder
                $move_folder = flextype('media_folders')->move($path, $new_path);

                $response_data = [];

                if ($move_folder) {
                    $response_data['data'] = flextype('media_folders')->fetchSingle($new_path);
                }

                // Set response code
                $response_code = $move_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($response_data));
            }

            return $response
                        ->withStatus($api_errors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0003']));
        }

        return $response
                    ->withStatus($api_errors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0003']));
    }

    return $response
                ->withStatus($api_errors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($api_errors['0003']));
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
flextype()->delete('/api/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('json')->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];

    if (flextype('registry')->get('flextype.settings.api.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('yaml')->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('json')->encode($api_errors['0501']));
                }

                // Delete folder
                $delete_folder = flextype('media_folders')->delete($path);

                // Set response code
                $response_code = $delete_folder ? 204 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($delete_folder));
            }

            return $response
                        ->withStatus($api_errors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0003']));
        }

        return $response
                    ->withStatus($api_errors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('json')->encode($api_errors['0003']));
    }

    return $response
                ->withStatus($api_errors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('json')->encode($api_errors['0003']));
});
