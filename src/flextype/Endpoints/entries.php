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
 * Validate entries entries token
 */
function validate_entries_token(string $token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/entries/' . $token . '/token.yaml')->exists();
}

/**
 * Fetch entry(entries)
 *
 * endpoint: GET /api/entries
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the entry(entries).
 * token  - [REQUIRED] - Valid Entries token.
 * filter - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of entry item objects.
 */
flextype()->get('/api/entries', function (Request $request, Response $response) use ($api_errors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response
                    ->withStatus($api_errors['0100']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0100']));
    }

    // Set variables
    $id      = $query['id'];
    $token   = $query['token'];
    $options = $query['options'] ?? [];
    $method  = $query['options']['method'] ?? '';

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries token
        if (validate_entries_token($token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';

            // Set entries token file
            if ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
                ) {
                    return $response
                                ->withStatus($api_errors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0003']));
                }

                // override entries.fetch.result
                flextype('registry')->set('flextype.settings.entries.fields.entries.fetch.result', 'toArray');

                if (isset($method) &&
                    strpos($method, 'fetch') !== false &&
                    is_callable([flextype('entries'), $method])) {
                    $fetchFromCallbackMethod = $method;
                } else {
                    $fetchFromCallbackMethod = 'fetch';
                }

                // Get fetch result
                $response_data['data'] = flextype('entries')->{$fetchFromCallbackMethod}($id, $options);
                $response_data['data'] = ($response_data['data'] instanceof Arrays) ? $response_data['data']->toArray() : $response_data['data'];

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
                }

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
 * Create entry
 *
 * endpoint: POST /api/entries
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
flextype()->post('/api/entries', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['data'])) {
        return $response
                    ->withStatus($api_errors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (
                ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
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

                // Create entry
                $create_entry = flextype('entries')->create($id, $data);

                if ($create_entry) {
                    $response_data['data'] = flextype('entries')->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $create_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
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
 * Update entry
 *
 * endpoint: PATCH /api/entries
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
flextype()->patch('/api/entries', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['data'])) {
        return $response
                    ->withStatus($api_errors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (
                ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
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

                // Update entry
                $update_entry = flextype('entries')->update($id, $data);

                if ($update_entry) {
                    $response_data['data'] = flextype('entries')->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $update_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
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
 * Move entry
 *
 * endpoint: PUT /api/entries
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
flextype()->put('/api/entries', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response
                    ->withStatus($api_errors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (
                ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
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

                // Move entry
                $move_entry = flextype('entries')->move($id, $new_id);

                // Get entry data
                if ($move_entry) {
                    $response_data['data'] = flextype('entries')->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $move_entry ? 200 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
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
 * Copy entry(entries)
 *
 * endpoint: PUT /api/entries/copy
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
flextype()->put('/api/entries/copy', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id']) || ! isset($post_data['new_id'])) {
        return $response
                    ->withStatus($api_errors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $new_id       = $post_data['new_id'];

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (
                ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
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

                // Copy entry
                $copy_entry = flextype('entries')->copy($id, $new_id, true);

                // Get entry data
                if ($copy_entry === null) {
                    $response_data['data'] = flextype('entries')->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = $copy_entry === null ? 200 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
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
 * Delete entry
 *
 * endpoint: DELETE /api/entries
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the entry.
 * token        - [REQUIRED] - Valid Entries token.
 * access_token - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
flextype()->delete('/api/entries', function (Request $request, Response $response) use ($api_errors) {
    // Get Post Data
    $post_data = (array) $request->getParsedBody();

    if (! isset($post_data['token']) || ! isset($post_data['access_token']) || ! isset($post_data['id'])) {
        return $response
                    ->withStatus($api_errors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($api_errors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];

    if (flextype('registry')->get('flextype.settings.api.entries.enabled')) {
        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path  = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (
                ($entries_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($entries_token_file_path)->get())) &&
                ($access_token_file_data = flextype('serializers')->yaml()->decode(filesystem()->file($access_token_file_path)->get()))
            ) {
                if (
                    $entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])
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

                // Delete entry
                $delete_entry = flextype('entries')->delete($id);

                // Set response code
                $response_code = $delete_entry ? 204 : 404;

                // Update calls counter
                filesystem()->file($entries_token_file_path)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1])));

                if ($response_code === 404) {
                    // Return response
                    return $response
                                ->withStatus($api_errors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($api_errors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($response_code)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($delete_entry));
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
