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
 * Validate entries entries token
 */
function validate_entries_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/entries/' . $token . '/token.yaml');
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
$app->get('/api/entries', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id     = $query['id'];
    $token  = $query['token'];
    $filter = $query['filter'] ?? null;

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries token
        if (validate_entries_token($token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/' . $token. '/token.yaml';

            // Set entries token file
            if ($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) {
                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Fetch entry
                $response_data['data'] = $flextype['entries']->fetch($id, $filter);

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

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
$app->post('/api/entries', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Post Data
    $post_data = $request->getParsedBody();

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) &&
                ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Create entry
                $create_entry = $flextype['entries']->create($id, $data);

                if ($create_entry) {
                    $response_data['data'] = $flextype['entries']->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = ($create_entry) ? 200 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

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
$app->patch('/api/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Post Data
    $post_data = $request->getParsedBody();

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];
    $data         = $post_data['data'];

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) &&
                ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Update entry
                $update_entry = $flextype['entries']->update($id, $data);

                if ($update_entry) {
                    $response_data['data'] = $flextype['entries']->fetch($id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = ($update_entry) ? 200 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

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

/**
 * Rename entry
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
 * Returns the entry item object for the entry item that was just renamed.
 */
$app->put('/api/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Post Data
    $post_data = $request->getParsedBody();

    // Set variables
    $token         = $post_data['token'];
    $access_token  = $post_data['access_token'];
    $id            = $post_data['id'];
    $new_id        = $post_data['new_id'];

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) &&
                ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Rename entry
                $rename_entry = $flextype['entries']->rename($id, $new_id);

                // Get entry data
                if ($rename_entry) {
                    $response_data['data'] = $flextype['entries']->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = ($rename_entry) ? 200 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

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
$app->put('/api/entries/copy', function (Request $request, Response $response) use ($flextype) {

    // Get Post Data
    $post_data = $request->getParsedBody();

    // Set variables
    $token         = $post_data['token'];
    $access_token  = $post_data['access_token'];
    $id            = $post_data['id'];
    $new_id        = $post_data['new_id'];

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) &&
                ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Copy entry
                $copy_entry = $flextype['entries']->copy($id, $new_id, true);

                // Get entry data
                if ($copy_entry === null) {
                    $response_data['data'] = $flextype['entries']->fetch($new_id);
                } else {
                    $response_data['data'] = [];
                }

                // Set response code
                $response_code = ($copy_entry) ? 200 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

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
$app->delete('/api/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Post Data
    $post_data = $request->getParsedBody();

    // Set variables
    $token        = $post_data['token'];
    $access_token = $post_data['access_token'];
    $id           = $post_data['id'];

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {

        // Validate entries and access token
        if (validate_entries_token($token) && validate_access_token($access_token)) {
            $entries_token_file_path = PATH['project'] . '/tokens/entries/' . $token . '/token.yaml';
            $access_token_file_path = PATH['project'] . '/tokens/access/' . $access_token . '/token.yaml';

            // Set entries and access token file
            if (($entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($entries_token_file_path), 'yaml')) &&
                ($access_token_file_data = $flextype['serializer']->decode(Filesystem::read($access_token_file_path), 'yaml'))) {

                if ($entries_token_file_data['state'] === 'disabled' ||
                    ($entries_token_file_data['limit_calls'] !== 0 && $entries_token_file_data['calls'] >= $entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                if ($access_token_file_data['state'] === 'disabled' ||
                    ($access_token_file_data['limit_calls'] !== 0 && $access_token_file_data['calls'] >= $access_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Delete entry
                $delete_entry = $flextype['entries']->delete($id);

                // Set response code
                $response_code = ($delete_entry) ? 204 : 404;

                // Update calls counter
                Filesystem::write($entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($entries_token_file_data, ['calls' => $entries_token_file_data['calls'] + 1]), 'yaml'));

                if ($response_code == 404) {

                    // Return response
                    return $response
                           ->withJson($api_sys_messages['NotFound'], $response_code);
                }

                // Return response
                return $response
                       ->withJson($delete_entry, $response_code);
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
