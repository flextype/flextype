<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function Flextype\entries;
use function count;

class Entries extends Api
{
    /**
     * Fetch entry.
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
                'api' => 'entries',
                'params' => ['token', 'id'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Get entry data
        $entryData = entries()->fetch($requestQueryParams['id'], $requestQueryParams['options'] ?? [])->toArray();

        if (count($entryData) > 0) {
            return $this->getApiResponse($response, $entryData, 200);
        }

        return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
    }

    /**
     * Create entry.
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
                'api' => 'entries',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Create new entry
        entries()->create($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch entry
        $entryData = entries()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($entryData) > 0) {
            return $this->getApiResponse($response, $entryData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Update entry.
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
                'api' => 'entries',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Update entry
        entries()->update($requestParsedBody['id'], $requestParsedBody['data'] ?? []);

        // Fetch entry
        $entryData = entries()->fetch($requestParsedBody['id'])->toArray();

        // Return response
        if (count($entryData) > 0) {
            return $this->getApiResponse($response, $entryData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Move entry.
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
                'api' => 'entries',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Move entry
        entries()->move($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch entry
        $entryData = entries()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($entryData) > 0) {
            return $this->getApiResponse($response, $entryData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Copy entry.
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
                'api' => 'entries',
                'params' => ['token', 'id', 'new_id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy entry
        entries()->copy($requestParsedBody['id'], $requestParsedBody['new_id']);

        // Fetch entry
        $entryData = entries()->fetch($requestParsedBody['new_id'])->toArray();

        // Return response
        if (count($entryData) > 0) {
            return $this->getApiResponse($response, $entryData, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }

    /**
     * Delete entry.
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
                'api' => 'entries',
                'params' => ['token', 'id', 'access_token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Copy entry
        entries()->delete($requestParsedBody['id']);

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}
