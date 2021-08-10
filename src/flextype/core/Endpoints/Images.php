<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Images extends Endpoints
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
        // Get Query Params
        $queryParams = $request->getQueryParams();

        // Check is utils api enabled
        if (! registry()->get('flextype.settings.api.images.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($queryParams['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($queryParams['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }

        // Fetch token
        $tokenData = tokens()->fetch($queryParams['token']);

        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($queryParams['token'], ['calls' => $tokenData['calls'] + 1]);

        // Check is file exists
        if (! filesystem()->file(PATH['project'] . '/uploads/' . $path)->exists()) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
        }

        // Return image response
        return container()->get('images')->getImageResponse($path, $queryParams);
    }
}
