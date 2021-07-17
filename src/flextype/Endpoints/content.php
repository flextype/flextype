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
function validateContentToken(string $token): bool
{
    return filesystem()->file(PATH['project'] . '/tokens/content/' . $token . '/token.yaml')->exists();
}

/**
 * Fetch content(content)
 *
 * endpoint: GET /api/content
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the content(content).
 * token  - [REQUIRED] - Valid Entries token.
 * filter - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of content item objects.
 */
flextype()->get('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Query Params
    $query = $request->getQueryParams();

    if (! isset($query['id']) || ! isset($query['token'])) {
        return $response
                    ->withStatus($apiErrors['0100']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0100']));
    }

    // Set variables
    $id      = $query['id'];
    $token   = $query['token'];
    $options = $query['options'] ?? [];
    $method  = $query['options']['method'] ?? '';

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content token
        if (validateContentToken($token)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';

            // Set content token file
            if ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // override content.fetch.result
                flextype('registry')->set('flextype.settings.content.content.fields.content.fetch.result', 'toArray');

                if (isset($method) &&
                    strpos($method, 'fetch') !== false &&
                    is_callable([flextype('content'), $method])) {
                    $fetchFromCallbackMethod = $method;
                } else {
                    $fetchFromCallbackMethod = 'fetch';
                }

                // Get fetch result
                $responseData['data'] = flextype('content')->{$fetchFromCallbackMethod}($id, $options);
                $responseData['data'] = ($responseData['data'] instanceof Arrays) ? $responseData['data']->toArray() : $responseData['data'];

                // Set response code
                $responseCode = count($responseData['data']) > 0 ? 200 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                return $response
                            ->withStatus($responseCode)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($responseData));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});

/**
 * Create content
 *
 * endpoint: POST /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * token         - [REQUIRED] - Valid Entries token.
 * accessToken  - [REQUIRED] - Valid Access token.
 * data          - [REQUIRED] - Data to store for the content.
 *
 * Returns:
 * Returns the content item object for the content item that was just created.
 */
flextype()->post('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $postData = (array) $request->getParsedBody();

    if (! isset($postData['token']) || ! isset($postData['access_token']) || ! isset($postData['id']) || ! isset($postData['data'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $postData['token'];
    $accessToken  = $postData['access_token'];
    $id           = $postData['id'];
    $data         = $postData['data'];

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateContentToken($token) && validate_accessToken($accessToken)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $accessTokenFilePath  = PATH['project'] . '/tokens/access/' . $accessToken . '/token.yaml';

            // Set content and access token file
            if (
                ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) &&
                ($accessTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($accessTokenFilePath)->get()))
            ) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                if (
                    $accessTokenFileData['state'] === 'disabled' ||
                    ($accessTokenFileData['limit_calls'] !== 0 && $accessTokenFileData['calls'] >= $accessTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // Create content
                $createContent = flextype('content')->create($id, $data);

                if ($createContent) {
                    $responseData['data'] = flextype('content')->fetch($id);
                } else {
                    $responseData['data'] = [];
                }

                // Set response code
                $responseCode = $createContent ? 200 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                       ->withStatus($responseCode)
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($responseData));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});

/**
 * Update content
 *
 * endpoint: PATCH /api/content
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the content.
 * token        - [REQUIRED] - Valid Entries token.
 * accessToken  - [REQUIRED] - Valid Authentication token.
 * data         - [REQUIRED] - Data to update for the content.
 *
 * Returns:
 * Returns the content item object for the content item that was just updated.
 */
flextype()->patch('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $postData = (array) $request->getParsedBody();

    if (! isset($postData['token']) || ! isset($postData['access_token']) || ! isset($postData['id']) || ! isset($postData['data'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $postData['token'];
    $accessToken  = $postData['access_token'];
    $id           = $postData['id'];
    $data         = $postData['data'];

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateContentToken($token) && validate_accessToken($accessToken)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $accessTokenFilePath  = PATH['project'] . '/tokens/access/' . $accessToken . '/token.yaml';

            // Set content and access token file
            if (
                ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) &&
                ($accessTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($accessTokenFilePath)->get()))
            ) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                if (
                    $accessTokenFileData['state'] === 'disabled' ||
                    ($accessTokenFileData['limit_calls'] !== 0 && $accessTokenFileData['calls'] >= $accessTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // Update content
                $update_content = flextype('content')->update($id, $data);

                if ($update_content) {
                    $responseData['data'] = flextype('content')->fetch($id);
                } else {
                    $responseData['data'] = [];
                }

                // Set response code
                $responseCode = $update_content ? 200 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($responseCode)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($responseData));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});

/**
 * Move content
 *
 * endpoint: PUT /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * newId        - [REQUIRED] - New Unique identifier of the content.
 * token         - [REQUIRED] - Valid Entries token.
 * accessToken  - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns the content item object for the content item that was just moved.
 */
flextype()->put('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $postData = (array) $request->getParsedBody();

    if (! isset($postData['token']) || ! isset($postData['access_token']) || ! isset($postData['id']) || ! isset($postData['new_id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $postData['token'];
    $accessToken  = $postData['access_token'];
    $id           = $postData['id'];
    $newId       = $postData['new_id'];

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateContentToken($token) && validate_accessToken($accessToken)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $accessTokenFilePath  = PATH['project'] . '/tokens/access/' . $accessToken . '/token.yaml';

            // Set content and access token file
            if (
                ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) &&
                ($accessTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($accessTokenFilePath)->get()))
            ) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                if (
                    $accessTokenFileData['state'] === 'disabled' ||
                    ($accessTokenFileData['limit_calls'] !== 0 && $accessTokenFileData['calls'] >= $accessTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // Move content
                $move_content = flextype('content')->move($id, $newId);

                // Get content data
                if ($move_content) {
                    $responseData['data'] = flextype('content')->fetch($newId);
                } else {
                    $responseData['data'] = [];
                }

                // Set response code
                $responseCode = $move_content ? 200 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($responseCode)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($responseData));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});

/**
 * Copy content(content)
 *
 * endpoint: PUT /api/content/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * newId        - [REQUIRED] - New Unique identifier of the content.
 * token         - [REQUIRED] - Valid Entries token.
 * accessToken  - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns the content item object for the content item that was just copied.
 */
flextype()->put('/api/content/copy', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $postData = (array) $request->getParsedBody();

    if (! isset($postData['token']) || ! isset($postData['access_token']) || ! isset($postData['id']) || ! isset($postData['new_id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $postData['token'];
    $accessToken  = $postData['access_token'];
    $id           = $postData['id'];
    $newId        = $postData['new_id'];

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateContentToken($token) && validate_accessToken($accessToken)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $accessTokenFilePath  = PATH['project'] . '/tokens/access/' . $accessToken . '/token.yaml';

            // Set content and access token file
            if (
                ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) &&
                ($accessTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($accessTokenFilePath)->get()))
            ) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                if (
                    $accessTokenFileData['state'] === 'disabled' ||
                    ($accessTokenFileData['limit_calls'] !== 0 && $accessTokenFileData['calls'] >= $accessTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // Copy content
                $copy_content = flextype('content')->copy($id, $newId, true);

                // Get content data
                if ($copy_content === null) {
                    $responseData['data'] = flextype('content')->fetch($newId);
                } else {
                    $responseData['data'] = [];
                }

                // Set response code
                $responseCode = $copy_content === null ? 200 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($responseCode)
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($responseData));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});

/**
 * Delete content
 *
 * endpoint: DELETE /api/content
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the content.
 * token        - [REQUIRED] - Valid Entries token.
 * accessToken - [REQUIRED] - Valid Authentication token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
flextype()->delete('/api/content', function (Request $request, Response $response) use ($apiErrors) {
    // Get Post Data
    $postData = (array) $request->getParsedBody();

    if (! isset($postData['token']) || ! isset($postData['access_token']) || ! isset($postData['id'])) {
        return $response
                    ->withStatus($apiErrors['0101'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0101']['http_status_code']));
    }

    // Set variables
    $token        = $postData['token'];
    $accessToken = $postData['access_token'];
    $id           = $postData['id'];

    if (flextype('registry')->get('flextype.settings.api.content.enabled')) {
        // Validate content and access token
        if (validateContentToken($token) && validate_accessToken($accessToken)) {
            $contentTokenFilePath = PATH['project'] . '/tokens/content/' . $token . '/token.yaml';
            $accessTokenFilePath  = PATH['project'] . '/tokens/access/' . $accessToken . '/token.yaml';

            // Set content and access token file
            if (
                ($contentTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($contentTokenFilePath)->get())) &&
                ($accessTokenFileData = flextype('serializers')->yaml()->decode(filesystem()->file($accessTokenFilePath)->get()))
            ) {
                if (
                    $contentTokenFileData['state'] === 'disabled' ||
                    ($contentTokenFileData['limit_calls'] !== 0 && $contentTokenFileData['calls'] >= $contentTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                if (
                    $accessTokenFileData['state'] === 'disabled' ||
                    ($accessTokenFileData['limit_calls'] !== 0 && $accessTokenFileData['calls'] >= $accessTokenFileData['limit_calls'])
                ) {
                    return $response
                                ->withStatus($apiErrors['0003']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
                }

                // Delete content
                $delete_content = flextype('content')->delete($id);

                // Set response code
                $responseCode = $delete_content ? 204 : 404;

                // Update calls counter
                filesystem()->file($contentTokenFilePath)->put(flextype('serializers')->yaml()->encode(array_replace_recursive($contentTokenFileData, ['calls' => $contentTokenFileData['calls'] + 1])));

                if ($responseCode === 404) {
                    // Return response
                    return $response
                                ->withStatus($apiErrors['0102']['http_status_code'])
                                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                                ->write(flextype('serializers')->json()->encode($apiErrors['0102']));
                }

                // Return response
                return $response
                            ->withStatus($responseCode)
                            ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                            ->write(flextype('serializers')->json()->encode($delete_content));
            }

            return $response
                        ->withStatus($apiErrors['0003']['http_status_code'])
                        ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                        ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
        }

        return $response
                    ->withStatus($apiErrors['0003']['http_status_code'])
                    ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                    ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
    }

    return $response
                ->withStatus($apiErrors['0003']['http_status_code'])
                ->withHeader('Content-Type', 'application/json;charset=' . flextype('registry')->get('flextype.settings.charset'))
                ->write(flextype('serializers')->json()->encode($apiErrors['0003']));
});
