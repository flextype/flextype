<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function content;
use function count;

class Entries extends Api
{
    /**
     * Fetch content.
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
                'api' => 'content',
                'params' => ['token', 'id'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Get content data
        $contentData = entries()->fetch($requestQueryParams['id'], $requestQueryParams['options'] ?? [])->toArray();

        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
    }

    /**
     * Create content.
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
                'api' => 'content',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Create new content
        entries()->create($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch content
        $contentData = entries()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Update content.
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
                'api' => 'content',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Update content
        entries()->update($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch content
        $contentData = entries()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Move content.
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
                'api' => 'content',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Move content
        entries()->move($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch content
        $contentData = entries()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Copy content.
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
                'api' => 'content',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy content
        entries()->copy($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch content
        $contentData = entries()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Delete content.
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
                'api' => 'content',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy content
        entries()->delete($requestParsedBody['id']);

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}
