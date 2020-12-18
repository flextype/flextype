<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use Atomastic\Arrays\Arrays;

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
    return filesystem()->file(PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml')->exists();
}

/**
 * Validate folders token
 */
function validate_folders_token($token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml')->exists();
}

/**
 * Fetch file(s)
 *
 * endpoint: GET /api/media/files
 *
 * Query:
 * path   - [REQUIRED] - Unique identifier of the file path.
 * token  - [REQUIRED] - Valid Files token.
 *
 * Returns:
 * An array of file item objects.
 */
flextype()->get('/api/media/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response->withStatus($api_errors['0500']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0500']));
    }

    // Set variables
    $id  = $query['id'];
    $token = $query['token'];
    $options = $query['options'] ?? [];
    $method  = $query['options']['method'] ?? '';

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate delivery token
        if (validate_files_token($token)) {
            $files_token_file_path = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';

            // Set delivery token file
            if ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Create files array
                $files = [];

                if (isset($method) &&
                    strpos($method, 'fetch') !== false &&
                    is_callable([flextype('media')->files(), $method])) {
                    $fetchFromCallbackMethod = $method;
                } else {
                    $fetchFromCallbackMethod = 'fetch';
                }

                // Get fetch result
                $files = flextype('media')->files()->{$fetchFromCallbackMethod}($id, $options);
                $files = ($files instanceof Arrays) ? $files->toArray() : $files;

                // Write response data
                $response_data         = [];
                $response_data['data'] = $files;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Upload media file
 *
 * endpoint: POST /api/media/files
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
flextype()->post('/api/media/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['folder']) || ! isset($_FILES['file'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $folder       = $post_data['folder'];
    $file         = $_FILES['file'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Create file
                $create_file = flextype('media')->files()->upload($file, $folder);

                $response_data['data'] = [];

                if ($create_file) {
                    $response_data['data'] = flextype('media')->files()->fetch($folder . '/' . basename($create_file));
                }

                // Set response code
                $response_code = filesystem()->file($create_file)->exists() ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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


/**
 * Rename media file
 *
 * endpoint: PUT /api/media/files
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
flextype()->put('/api/media/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];
    $new_id     = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Rename file
                $rename_file = flextype('media')->files()->move($id, $new_id);

                $response_data['data'] = [];

                if ($rename_file) {
                    $response_data['data'] = flextype('media')->files()->fetch($new_id);
                }

                // Set response code
                $response_code = $rename_file === true ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Copy media file
 *
 * endpoint: PUT /api/media/files
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
flextype()->put('/api/media/files/copy', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];
    $new_id     = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Copy file
                $copy_file = flextype('media')->files()->copy($id, $new_id);

                $response_data['data'] = [];

                if ($copy_file) {
                    $response_data['data'] = flextype('media')->files()->fetch($new_id);
                }

                // Set response code
                $response_code = $copy_file === true ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Delete file
 *
 * endpoint: DELETE /api/media/files
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the file.
 * token        - [REQUIRED] - Valid Entries token.
 * access_token - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
flextype()->delete('/api/media/files', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['id']) || ! isset($post_data['access_token']) || ! isset($post_data['id'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Delete file
                $delete_file = flextype('media')->files()->delete($id);

                // Set response code
                $response_code = $delete_file ? 204 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->get(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($delete_file));
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

/**
 * Update file meta information
 *
 * endpoint: PATCH /api/media/files/meta
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
flextype()->patch('/api/media/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['field']) || ! isset($post_data['value'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $field        = $post_data['field'];
    $value        = $post_data['value'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get($access_token_file_path)))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Update file meta
                $update_file_meta = flextype('media')->files()->meta()->update($id, $field, $value);

                $response_data['data'] = [];

                if ($update_file_meta) {
                    $response_data['data'] = flextype('media')->files()->fetch($id);
                }

                // Set response code
                $response_code = $update_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Add file meta information
 *
 * endpoint: POST /api/media/files/meta
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
flextype()->post('/api/media/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['field']) || ! isset($post_data['value'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $field        = $post_data['field'];
    $value        = $post_data['value'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Add file meta
                $add_file_meta = flextype('media')->files()->meta()->add($id, $field, $value);

                $response_data['data'] = [];

                if ($add_file_meta) {
                    $response_data['data'] = flextype('media')->files()->fetch($id);
                }

                // Set response code
                $response_code = $add_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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


/**
 * Delete file meta information
 *
 * endpoint: DELETE /api/media/files/meta
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
flextype()->delete('/api/media/files/meta', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['field'])) {
        return $response->withStatus($api_errors['0501']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0501']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];
    $field        = $post_data['field'];

    if (flextype('registry')->get('flextype.settings.api.media.files.enabled')) {
        // Validate files and access token
        if (validate_files_token($token) && validate_access_token($access_token)) {
            $files_token_file_path  = PATH['project'] . '/tokens/media/files/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($files_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($files_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $files_token_file_data['state'] === 'disabled' ||
                    ($files_token_file_data['limit_calls'] !== 0 && $files_token_file_data['calls'] >= $files_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // Delete file meta
                $delete_file_meta = flextype('media')->files()->meta()->delete($id, $field);

                $response_data['data'] = [];

                if ($delete_file_meta) {
                    $response_data['data'] = flextype('media')->files()->fetch($id);
                }

                // Set response code
                $response_code = $delete_file_meta ? 200 : 404;

                // Update calls counter
                filesystem()->file($files_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($files_token_file_data, ['calls' => $files_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0502']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0502']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($response_data));
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


/**
 * Fetch folders(s)
 *
 * endpoint: GET /api/media/folders
 *
 * Query:
 * path       - [REQUIRED] - Folder path.
 * collection - [OPTIONAL] - Collection or single.
 * token      - [REQUIRED] - Valid Folders token.
 *
 * Returns:
 * An array of folder(s) item objects.
 */
flextype()->get('/api/media/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response->withStatus($api_errors['0600']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0600']));
    }

    // Set variables
    $id  = $query['id'];
    $token = $query['token'];
    $options = $query['options'] ?? [];
    $method  = $query['method'] ?? '';

    if (flextype('registry')->get('flextype.settings.api.media.folders.enabled')) {
        // Validate delivery token
        if (validate_folders_token($token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml';

            // Set delivery token file
            if ($folders_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($folders_token_file_path)->get())) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                // Create folders array
                $folders = [];

                if (isset($method) &&
                    strpos($method, 'fetch') !== false &&
                    is_callable([flextype('media')->folders(), $method])) {
                    $fetchFromCallbackMethod = $method;
                } else {
                    $fetchFromCallbackMethod = 'fetch';
                }

                // Get fetch result
                $folders = flextype('media')->folders()->{$fetchFromCallbackMethod}($id, $options);
                $folders = ($folders instanceof Arrays) ? $folders->toArray() : $folders;

                // Write response data
                $response_data         = [];
                $response_data['data'] = $folders;

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                           ->withStatus($api_errors['0602']['http_status_code'])
                           ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                           ->write(flextype('serializers')->json()->encode($api_errors['0602']));
                }

                // Return response
                return $response
                       ->withStatus($response_code)
                       ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                       ->write(flextype('serializers')->json()->encode($response_data));
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


/**
 * Create folder
 *
 * endpoint: PUT /api/media/folders
 *
 * Body:
 * path          - [REQUIRED] - New folder path.
 * token         - [REQUIRED] - Valid Folders token.
 * access_token  - [REQUIRED] - Valid Access token.
 *
 * Returns:
 * Returns the folder object for the folder that was just created.
 */
flextype()->post('/api/media/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('serializers')->json()->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];

    if (flextype('registry')->get('flextype.settings.api.media.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                // Create folder
                $create_folder = flextype('media')->folders()->create($id);

                $response_data = [];

                if ($create_folder) {
                    $response_data['data'] = flextype('media')->folders()->fetch($id);
                }

                // Set response code
                $response_code = $create_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                         ->withStatus($api_errors['0602']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                     ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                     ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Copy folder
 *
 * endpoint: PUT /api/media/folders/copy
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
flextype()->put('/api/media/folders/copy', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('serializers')->json()->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.media.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0601']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0601']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0601']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0601']));
                }

                // Copy folder
                $copy_folder = flextype('media')->folders()->copy($id, $new_id);

                $response_data = [];

                if ($copy_folder) {
                    $response_data['data'] = flextype('media')->folders()->fetch($new_id);
                } else {
                    $response_data['data'] = $copy_folder;
                }

                // Set response code
                $response_code = $copy_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($response_data));
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

/**
 * Move folder
 *
 * endpoint: PUT /api/media/folders
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
flextype()->put('/api/media/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('serializers')->json()->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.media.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                // Rename folder
                $move_folder = flextype('media')->folders()->move($id, $new_id);

                $response_data = [];

                if ($move_folder) {
                    $response_data['data'] = flextype('media')->folders()->fetch($new_id);
                }

                // Set response code
                $response_code = $move_folder ? 200 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($response_data));
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

/**
* Delete folder
*
* endpoint: DELETE /api/media/folders
*
* Body:
* id           - [REQUIRED] - Unique identifier of the folder.
* token        - [REQUIRED] - Valid Folders token.
* access_token - [REQUIRED] - Valid Authentication token.
*
* Returns:
* Returns an empty body with HTTP status 204
*/
flextype()->delete('/api/media/folders', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id'])) {
        return $response->withStatus($api_errors['0601']['http_status_code'])
            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
            ->write(flextype('serializers')->json()->encode($api_errors['0601']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id         = $post_data['id'];

    if (flextype('registry')->get('flextype.settings.api.media.folders.enabled')) {
        // Validate files and access token
        if (validate_folders_token($token) && validate_access_token($access_token)) {
            $folders_token_file_path = PATH['project'] . '/tokens/media/folders/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set files and access token file
            if (
                ($folders_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($folders_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $folders_token_file_data['state'] === 'disabled' ||
                    ($folders_token_file_data['limit_calls'] !== 0 && $folders_token_file_data['calls'] >= $folders_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response->withStatus($api_errors['0501']['http_status_code'])
                                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                    ->write(flextype('serializers')->json()->encode($api_errors['0501']));
                }

                // Delete folder
                $delete_folder = flextype('media')->folders()->delete($id);

                // Set response code
                $response_code = $delete_folder ? 204 : 404;

                // Update calls counter
                filesystem()->file($folders_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($folders_token_file_data, ['calls' => $folders_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0602']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0602']));
                }

                // Return response
                return $response
                     ->withStatus($response_code)
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($delete_folder));
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
