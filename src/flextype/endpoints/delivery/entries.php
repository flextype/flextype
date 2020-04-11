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
 * Validate delivery entries token
 */
function validate_delivery_entries_token($token) : bool
{
    return Filesystem::has(PATH['site'] . '/tokens/delivery/entries/' . $token . '/token.yaml');
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
$app->get('/api/delivery/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id     = $query['id'];
    $token  = $query['token'];
    $filter = $query['filter'] ?? null;

    if ($flextype['registry']->get('flextype.settings.api.entries.enabled')) {
        // Validate delivery token
        if (validate_delivery_entries_token($token)) {
            $delivery_entries_token_file_path = PATH['site'] . '/tokens/delivery/entries/' . $token. '/token.yaml';

            // Set delivery token file
            if ($delivery_entries_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_entries_token_file_path), 'yaml')) {
                if ($delivery_entries_token_file_data['state'] === 'disabled' ||
                    ($delivery_entries_token_file_data['limit_calls'] !== 0 && $delivery_entries_token_file_data['calls'] >= $delivery_entries_token_file_data['limit_calls'])) {
                    return $response->withJson(['detail' => 'Incorrect authentication credentials.'], 401);
                }

                // Fetch entry
                $data['data'] = $flextype['entries']->fetch($id, $filter);

                // Set response code
                $response_code = count($data['data']) > 0 ? 200 : 404;

                // Update calls counter
                Filesystem::write($delivery_entries_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_entries_token_file_data, ['calls' => $delivery_entries_token_file_data['calls'] + 1]), 'yaml'));

                // Return response
                return $response
                       ->withJson($data, $response_code)
                       ->withHeader('Access-Control-Allow-Origin', '*');
            }

            return $response
                   ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
                   ->withHeader('Access-Control-Allow-Origin', '*');
        }

        return $response
               ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
               ->withHeader('Access-Control-Allow-Origin', '*');
    }

    return $response
           ->withJson(['detail' => 'Incorrect authentication credentials.'], 401)
           ->withHeader('Access-Control-Allow-Origin', '*');
});
