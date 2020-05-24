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

/**
 * API sys messages
 */
$api_sys_messages['AccessTokenInvalid'] = ['sys' => ['type' => 'Error', 'id' => 'AccessTokenInvalid'], 'message' => 'The access token you sent could not be found or is invalid.'];
$api_sys_messages['NotFound'] = ['sys' => ['type' => 'Error', 'id' => 'NotFound'], 'message' => 'The resource could not be found.'];

/**
 * Validate delivery config token
 */
function validate_delivery_config_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/delivery/config/' . $token . '/token.yaml');
}

/**
 * Fetch item in the config
 *
 * endpoint: GET /api/delivery/config
 *
 * Query:
 * key     - [REQUIRED] - Unique identifier of the config item.
 * config  - [REQUIRED] - Unique identifier of the config namespace.
 * token   - [REQUIRED] - Valid Content Delivery API token for Config.
 *
 * Returns:
 * An array of config item objects.
 */
$app->get('/api/delivery/config', function (Request $request, Response $response) use ($flextype, $api_sys_messages) {

    // Get Query Params
    $query = $request->getQueryParams();

    // Set variables
    $key      = $query['key'];
    $config   = $query['config'];
    $token    = $query['token'];

    if ($flextype['registry']->get('flextype.settings.api.delivery.config.enabled')) {

        // Validate delivery token
        if (validate_delivery_config_token($token)) {
            $delivery_config_token_file_path = PATH['project'] . '/tokens/delivery/config/' . $token . '/token.yaml';

            // Set delivery token file
            if ($delivery_config_token_file_data = $flextype['serializer']->decode(Filesystem::read($delivery_config_token_file_path), 'yaml')) {
                if ($delivery_config_token_file_data['state'] === 'disabled' ||
                    ($delivery_config_token_file_data['limit_calls'] !== 0 && $delivery_config_token_file_data['calls'] >= $delivery_config_token_file_data['limit_calls'])) {
                    return $response->withJson($api_sys_messages['AccessTokenInvalid'], 401);
                }

                // Fetch config
                if ($flextype['config']->has($config, $key)) {
                    $response_data['data']['key']   = $key;
                    $response_data['data']['value'] = $flextype['config']->get($config, $key);

                    // Set response code
                    $response_code = 200;

                } else {
                    $response_data = [];
                    $response_code = 404;
                }

                // Update calls counter
                Filesystem::write($delivery_config_token_file_path, $flextype['serializer']->encode(array_replace_recursive($delivery_config_token_file_data, ['calls' => $delivery_config_token_file_data['calls'] + 1]), 'yaml'));

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
