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
use function basename;
use function count;
use function filesystem;
use function flextype;
use function is_dir;

/**
 * Validate files token
 */
function validate_files_token($token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/files/' . $token . '/token.yaml')->exists();
}

/**
 * Fetch file(s)
 *
 * endpoint: GET /api/files
 *
 * Query:
 * path   - [REQUIRED] - Unique identifier of the file path.
 * token  - [REQUIRED] - Valid Files token.
 *
 * Returns:
 * An array of file item objects.
 */
flextype()->get('/api/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['path']) || ! isset($query['token'])) {
        return $response->withStatus($api_errors['0500']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0500']));
    }

    // Set variables
    $path  = $query['path'];
    $token = $query['token'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate delivery token
        if (validate_files_token($token)) {
            $files_token_file_path = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';

            // Set delivery token file
            if ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Create files array
                $files = [];

                // Get list if file or files for specific folder
                if (is_dir(PATH['project'] . '/uploads/' . $path)) {
                    $files = flextype('media_files')->fetchCollection($path)->toArray();
                } else {
                    $files = flextype('media_files')->fetchSingle($path)->toArray();
                }

                // Write response data
                $response_data         = [];
                $response_data['data'] = $files;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Upload media file
 *
 * endpoint: POST /api/files
 *
 * Body:
 * folder        - [REQUIRED] - The folder you're targetting.
 * token         - [REQUIRED] - Valid Files token.
 * access_token  - [REQUIRED] - Valid Access token.
 * file          - [REQUIRED] - Raw file data (multipart/form-data).
 *
 * Returns:
 * Returns the file object for the file that was just uploaded.
 */
flextype()->post('/api/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['folder']) || ! isset($_FILES['file'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $folder       = $post_data['folder'];
    $file         = $_FILES['file'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Create file
                $create_file = flextype('media_files')->upload($file, $folder);

                $response_data['data'] = [];

                if ($create_file) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($folder . '/' . basename($create_file));
                }

                // Set response code
                $response_code = filesystem()->file($create_file)->exists() ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Rename media file
 *
 * endpoint: PUT /api/files
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the file.
 * new_id        - [REQUIRED] - New Unique identifier of the file.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
flextype()->put('/api/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Rename file
                $rename_file = flextype('media_files')->move($path, $new_path);

                $response_data['data'] = [];

                if ($rename_file) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($new_path);
                }

                // Set response code
                $response_code = $rename_file === true ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Copy media file
 *
 * endpoint: PUT /api/files
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the file.
 * new_id        - [REQUIRED] - New Unique identifier of the file.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
flextype()->put('/api/files/copy', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['new_path'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $new_path     = $post_data['new_path'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Copy file
                $copy_file = flextype('media_files')->copy($path, $new_path);

                $response_data['data'] = [];

                if ($copy_file) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($new_path);
                }

                // Set response code
                $response_code = $copy_file === true ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Delete file
 *
 * endpoint: DELETE /api/files
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the file.
 * token        - [REQUIRED] - Valid Entries token.
 * access_token - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
flextype()->delete('/api/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['path']) || ! isset($post_data['access_token']) || ! isset($post_data['path'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Delete file
                $delete_file = flextype('media_files')->delete($path);

                // Set response code
                $response_code = $delete_file ? 204 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->get(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('json')->encode($delete_file));
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
 * Update file meta information
 *
 * endpoint: PATCH /api/files/meta
 *
 * Body:
 * path          - [REQUIRED] - File path.
 * field         - [REQUIRED] - Field name.
 * value         - [REQUIRED] - Field value.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just updated.
 */
flextype()->patch('/api/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['field']) || ! isset($post_data['value'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $field        = $post_data['field'];
    $value        = $post_data['value'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get($access_token_file_path)))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Update file meta
                $update_file_meta = flextype('media_files_meta')->update($path, $field, $value);

                $response_data['data'] = [];

                if ($update_file_meta) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($path);
                }

                // Set response code
                $response_code = $update_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Add file meta information
 *
 * endpoint: POST /api/files/meta
 *
 * Body:
 * path          - [REQUIRED] - File path.
 * field         - [REQUIRED] - Field name.
 * value         - [REQUIRED] - Field value.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
flextype()->post('/api/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['field']) || ! isset($post_data['value'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $field        = $post_data['field'];
    $value        = $post_data['value'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Add file meta
                $add_file_meta = flextype('media_files_meta')->add($path, $field, $value);

                $response_data['data'] = [];

                if ($add_file_meta) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($path);
                }

                // Set response code
                $response_code = $add_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
 * Delete file meta information
 *
 * endpoint: DELETE /api/files/meta
 *
 * Body:
 * path          - [REQUIRED] - File path.
 * field         - [REQUIRED] - Field name.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the file object for the file that was just created.
 */
flextype()->delete('/api/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['path']) || ! isset($post_data['field'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('json')->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $path         = $post_data['path'];
    $field        = $post_data['field'];

    if (flextype('registry')->get('flextype.settings.api.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('yaml')->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('yaml')->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0003']));
                }

                // Delete file meta
                $delete_file_meta = flextype('media_files_meta')->delete($path, $field);

                $response_data['data'] = [];

                if ($delete_file_meta) {
                    $response_data['data'] = flextype('media_files')->fetchSingle($path);
                }

                // Set response code
                $response_code = $delete_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('yaml')->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('json')->encode($api_errors['0502']));
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
