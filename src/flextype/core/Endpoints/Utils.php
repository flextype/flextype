<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function count;
use function filesystem;

class Utils extends Api
{
    /**
     * Clear cache
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function clearCache(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'utils',
                'params' => ['token', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Clear cache
        filesystem()->directory(PATH['tmp'])->delete();

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}
