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
use function tokens;

class Tokens extends Api
{
    /**
     * Fetch token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function fetch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Query Params
        $requestQueryParams = $request->getQueryParams();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Get token data
        $contentData = tokens()->fetch($requestQueryParams['id'], $requestQueryParams['options'] ?? [])->toArray();

        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
    }

    /**
     * Create token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Create new token
        tokens()->create($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch token
        $contentData = tokens()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Update token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Update token
        tokens()->update($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch token
        $contentData = tokens()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Move token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function move(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Move token
        tokens()->move($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch token
        $contentData = tokens()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Copy token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function copy(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy token
        tokens()->copy($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch token
        $contentData = tokens()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Delete token.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'tokens',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy token
        tokens()->delete($requestParsedBody['id']);

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}
