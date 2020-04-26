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
 * API sys messages
 */
$api_sys_messages['AccessTokenInvalid'] = ['sys' => ['type' => 'Error', 'id' => 'AccessTokenInvalid'], 'message' => 'The access token you sent could not be found or is invalid.'];
$api_sys_messages['NotFound'] = ['sys' => ['type' => 'Error', 'id' => 'NotFound'], 'message' => 'The resource could not be found.'];

/**
 * Validate delivery entries token
 */
function validate_delivery_entries_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/delivery/entries/' . $token . '/token.yaml');
}

/**
 * Fetch entry(entries)
 *
 * endpoint: GET /api/delivery/entries
 *
 * Query:
 * id     - [REQUIRED] - Unique identifier of the entry(entries).
 * token  - [REQUIRED] - Valid Content Delivery API token for Entries.
 * filter - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of entry item objects.
 */
$app->get('/api/delivery/entries', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id     = $query['id'];
    $token  = $query['token'];
    $filter = $query['filter'] ?? null;

    if ($flextype['registry']->get('flextype.settings.api.delivery.entries.enabled')) {

        // Validate delivery token
        if (validate_delivery_entries_token($token)) {
            $delivery_entries_token_file_path = PATH['project'] . '/tokens/delivery/entries/' . $token. '/token.yaml';

            // Set delivery token file
            if ($delivery_entries_token_file_data = $flextype['serializer']->decode(Filesystem::read($delivery_entries_token_file_path), 'yaml')) {
                if ($delivery_entries_token_file_data['state'] === 'disabled' ||
                    ($delivery_entries_token_file_data['limit_calls'] !== 0 && $delivery_entries_token_file_data['calls'] >= $delivery_entries_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Fetch entry
                $response_data['data'] = $flextype['entries']->fetch($id, $filter);

                // Set response code
                $response_code = count($response_data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($delivery_entries_token_file_path, $flextype['serializer']->encode(array_replace_recursive($delivery_entries_token_file_data, ['calls' => $delivery_entries_token_file_data['calls'] + 1]), 'yaml'));

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
