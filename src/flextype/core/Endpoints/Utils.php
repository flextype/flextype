<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Flextype\Endpoints\Endpoints;

use function array_replace_recursive;
use function filesystem;
use function flextype;

class Utils extends Endpoints
{
    /**
     * Clear cache
     * 
     * @param ServerRequestInterface  $request  PSR7 request.
     * @param ResponseInterface       $response PSR7 response.
     * 
     * @return ResponseInterface Response.
     */
    public function clearCache(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Query Params
        $data = $request->getParsedBody();

        // Check is utils api enabled
        if (! registry()->get('flextype.settings.api.utils.enabled')) {
            return $this->getApiResponse($response, $this->statusCodeMessages['400'], 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->statusCodeMessages['400'], 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->statusCodeMessages['401'], 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (password_verify($tokenData['hashed_access_token'], $data['access_token'])) {
            return $this->getApiResponse($response, $this->statusCodeMessages['401'], 401);
        }

        // Check token state and limit_calls
        if ($tokenData['state'] === 'disabled' || 
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])) {
            return $this->getApiResponse($response, $this->statusCodeMessages['400'], 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Clear cache
        filesystem()->directory(PATH['tmp'])->delete();

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}