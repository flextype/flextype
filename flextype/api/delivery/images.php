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
 * Validate delivery images token
 */
function validate_delivery_images_token($request, $flextype) : bool
{
    return Filesystem::has(PATH['tokens'] . '/delivery/images/' . $request->getQueryParams()['token'] . '/token.yaml');
}

/**
 * Fetch image
 *
 * endpoint: /api/delivery/images
 */
$app->get('/api/delivery/images/{path:.+}', function (Request $request, Response $response, array $args) use ($flextype) {

    // Get Query Params
    $query = $request->getQueryParams();

    if ($flextype['registry']->get('flextype.api.images.enabled')) {

        // Validate delivery image token
        if (validate_delivery_images_token($request, $flextype)) {
            $delivery_images_token_file_path = PATH['tokens'] . '/delivery/images/' . $request->getQueryParams()['token'] . '/token.yaml';

            // Set delivery token file
            if ($delivery_images_token_file_data = $flextype['parser']->decode(Filesystem::read($delivery_images_token_file_path), 'yaml')) {
                if ($delivery_images_token_file_data['state'] == 'disabled' ||
                    ($delivery_images_token_file_data['limit_calls'] != 0 && $delivery_images_token_file_data['calls'] >= $delivery_images_token_file_data['limit_calls'])) {
                    return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
                } else {

                    // Update calls counter
                    Filesystem::write($delivery_images_token_file_path, $flextype['parser']->encode(array_replace_recursive($delivery_images_token_file_data, ['calls' => $delivery_images_token_file_data['calls'] + 1]), 'yaml'));

                    if (Filesystem::has(PATH['uploads'] . '/entries/' . $args['path'])) {
                        return $flextype['images']->getImageResponse($args['path'], $_GET);
                    } else {
                        return $response->withJson([], 404);
                    }
                }
            } else {
                return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
            }
        } else {
            return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
        }

        return $response->withStatus(404);
    } else {
        return $response->withJson(["detail" => "Incorrect authentication credentials."], 401);
    }
});
