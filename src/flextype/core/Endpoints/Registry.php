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
use function registry;

class Registry extends Api
{
    /**
     * Get registry item.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Query Params
        $requestQueryParams = $request->getQueryParams();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'registry',
                'params' => ['token', 'key'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Get registry data
        $registryData = registry()->get($requestQueryParams['key'], $requestQueryParams['default'] ?? null);

        return $this->getApiResponse($response, ['key' => $requestQueryParams['key'], 'value' => $registryData], 200);
    }
}
