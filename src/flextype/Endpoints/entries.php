<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;
use Atomastic\Arrays\Arrays;

use function array_replace_recursive;
use function count;
use function filesystem;
use function flextype;
use function is_array;

/**
 * Validate content content token
 */
function validateEntriesToken(string $token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/content/' . $token . '/token.yaml')->exists();
}

/**
 * Fetch entry(content)
 *
 * endpoint: GET /api/content
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the entry(content).
 * token  - [REQUIRED] - Valid Entries token.
 * filter - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of entry item objects.
 */
app()->get('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response
                    ->withStatus($apiErrors['0100']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0100']));
    }

    // Set variables
    $id      = $query['id'];
    $token   = $query['token'];
    $options = $query['options'] ?? [];
    $method  = $query['options']['method'] ?? '';

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content token
        if (validateEntriesToken($token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';

            // Set content token file
            if ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // override content.fetch.result
                registry()->set('flextype.settings.entries.content.fields.content.fetch.result', 'toArray');

                if (isset($method) &&
                    strpos($method, 'fetch') !== false &&
                    is_callable([content(), $method])) {
                    $fetchFromCallbackMethod = $method;
                } else {
                    $fetchFromCallbackMethod = 'fetch';
                }

                // Get fetch result
                $response_data['data'] = content()->{$fetchFromCallbackMethod}($id, $options);
                $response_data['data'] = ($response_data['data'] instanceof Arrays) ? $response_data['data']->toArray() : $response_data['data'];

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                            ->write(serializers()->json()->encode($response_data));
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

/**
 * Create entry
 *
 * endpoint: POST /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Access token.
 * data          - [REQUIRED] - Data to store for the entry.
 *
 * Returns:
 * Returns the entry item object for the entry item that was just created.
 */
flextype()->post('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['data'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateEntriesToken($token) && validate_access_token($access_token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set content and access token file
            if (
                ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) &&
                ($access_token_file_data = serializers()->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Create entry
                $create_entry = content()->create($id, $data);

                if ($create_entry) {
                    $response_data['data'] = content()->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $create_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                       ->withStatus($response_code)
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($response_data));
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

/**
 * Update entry
 *
 * endpoint: PATCH /api/content
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the entry.
 * token        - [REQUIRED] - Valid Entries token.
 * access_token - [REQUIRED] - Valid Authentication token.
 * data         - [REQUIRED] - Data to update for the entry.
 *
 * Returns:
 * Returns the entry item object for the entry item that was just updated.
 */
flextype()->patch('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['data'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateEntriesToken($token) && validate_access_token($access_token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set content and access token file
            if (
                ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) &&
                ($access_token_file_data = serializers()->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Update entry
                $update_entry = content()->update($id, $data);

                if ($update_entry) {
                    $response_data['data'] = content()->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $update_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                            ->write(serializers()->json()->encode($response_data));
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

/**
 * Move entry
 *
 * endpoint: PUT /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * new_id        - [REQUIRED] - New Unique identifier of the entry.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns the entry item object for the entry item that was just moved.
 */
flextype()->put('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateEntriesToken($token) && validate_access_token($access_token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set content and access token file
            if (
                ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) &&
                ($access_token_file_data = serializers()->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Move entry
                $move_entry = content()->move($id, $new_id);

                // Get entry data
                if ($move_entry) {
                    $response_data['data'] = content()->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $move_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                            ->write(serializers()->json()->encode($response_data));
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

/**
 * Copy entry(content)
 *
 * endpoint: PUT /api/content/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * new_id        - [REQUIRED] - New Unique identifier of the entry.
 * token         - [REQUIRED] - Valid Entries token.
 * access_token  - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns the entry item object for the entry item that was just copied.
 */
flextype()->put('/api/content/copy', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateEntriesToken($token) && validate_access_token($access_token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set content and access token file
            if (
                ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) &&
                ($access_token_file_data = serializers()->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Copy entry
                $copy_entry = content()->copy($id, $new_id, true);

                // Get entry data
                if ($copy_entry === null) {
                    $response_data['data'] = content()->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $copy_entry === null ? 200 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($response_data));
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

/**
 * Delete entry
 *
 * endpoint: DELETE /api/content
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the entry.
 * token        - [REQUIRED] - Valid Entries token.
 * access_token - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
flextype()->delete('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                    ->write(serializers()->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];

    if (registry()->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateEntriesToken($token) && validate_access_token($access_token)) {
            $content_token_file_path = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set content and access token file
            if (
                ($content_token_file_data = serializers()->yaml()->decode(filesystem()->file($content_token_file_path)->get())) &&
                ($access_token_file_data = serializers()->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $content_token_file_data['state'] === 'disabled' ||
                    ($content_token_file_data['limit_calls'] !== 0 && $content_token_file_data['calls'] >= $content_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                if (
                    $access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0003']));
                }

                // Delete entry
                $delete_entry = content()->delete($id);

                // Set response code
                $response_code = $delete_entry ? 204 : 404;

                // Update calls counter
                filesystem()->file($content_token_file_path)->put(serializers()->yaml()->encode(array_replace_recursive($content_token_file_data, ['calls' => $content_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                                ->write(serializers()->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'))
                            ->write(serializers()->json()->encode($delete_entry));
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