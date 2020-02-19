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

/**
 * Validate delivery entries token
 */
function validate_delivery_entries_token($request, $flextype) : bool
{
    return Filesystem::has(PATH['tokens'] . '/delivery/entries/' . $request->getQueryParams()['token'] . '/token.yaml');
}

/**
 * Fetch entry(entries)
 *
 * endpoint: /api/delivery/entries
 */
$app->get('/api/delivery/entries', function (Request $request, Response $response) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $id   = $query['id'];
    $args = isset($query['args']) ? $query['args'] : null;

    if ($flextype['registry']->get('flextype.api.entries.enabled')) {

        // Validate delivery token
        if (validate_delivery_entries_token($request, $flextype)) {
            $delivery_entries_token_file_path = PATH['tokens'] . '/delivery/entries/' . $request->getQueryParams()['token'] . '/token.yaml';

            // Set delivery token file
            if ($delivery_entries_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_entries_token_file_path), 'yaml')) {
                if ($delivery_entries_token_file_data['state'] == 'disabled' ||
                    ($delivery_entries_token_file_data['limit_calls'] != 0 && $delivery_entries_token_file_data['calls'] >= $delivery_entries_token_file_data['limit_calls'])) {
                    return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
                } else {
                    // Fetch entry
                    $data = $flextype['entries']->fetch($id, $args);

                    // Set response code
                    $response_code = (count($data) > 0) ? 200 : 404 ;

                    // Update calls counter
                    Filesystem::write($delivery_entries_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_entries_token_file_data, ['calls' => $delivery_entries_token_file_data['calls'] + 1]), 'yaml'));

                    // Return response
                    return $response->withJson($data, $response_code);
                }
            } else {
                return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
            }
        } else {
            return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
        }
    } else {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
    }
});
