<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function container;
use function count;
use function filesystem;

class Images extends Api
{
    /**
     * Fetch image.
     *
     * @param string                 $path     Image path.
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function fetch(string $path, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'images',
                'params' => ['token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Check is file exists
        if (! filesystem()->file(flextype()->registry()->get('flextype.settings.images.directory') . '/' . $path)->exists()) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
        }

        // Return image response
        return container()->get('images')->getImageResponse($path, $request->getQueryParams());
    }
}
