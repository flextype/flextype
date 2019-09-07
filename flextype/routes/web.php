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
 * Generates and returns the image response
 */
$app->get('/image/{path:.+}', function (Request $request, Response $response, array $args) use ($flextype) {
    if (Filesystem::has(PATH['entries'] . '/' . $args['path'])) {
        return $flextype['images']->getImageResponse($args['path'], $_GET);
    }

    return $response->withStatus(404);
});
