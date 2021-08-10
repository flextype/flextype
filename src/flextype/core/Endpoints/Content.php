<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Content extends Endpoints
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
        // Get Query Params
        $queryParams = $request->getQueryParams();

        // Check is utils api enabled
        if (! registry()->get('flextype.settings.api.content.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($queryParams['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($queryParams['id'])) {
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

        // Get content data
        $contentData = content()->fetch($queryParams['id'], $queryParams['options'] ?? [])->toArray();

        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        } else {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(404), 404);
        }
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
        // Get Parser Body
        $data = $request->getParsedBody();

        // Check is content api enabled
        if (! registry()->get('flextype.settings.api.images.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($data['id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (! password_verify($data['access_token'], $tokenData['hashed_access_token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Create new content
        content()->create($data['id'], $data['data'] ?? []);

        // Fetch content
        $contentData = content()->fetch($data['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        } else {
            return $this->getApiResponse($response, [], 404);
        }
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
        // Get Parser Body
        $data = $request->getParsedBody();

        // Check is content api enabled
        if (! registry()->get('flextype.settings.api.images.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($data['id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (! password_verify($data['access_token'], $tokenData['hashed_access_token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Update content
        content()->update($data['id'], $data['data'] ?? []);

        // Fetch content
        $contentData = content()->fetch($data['id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        } else {
            return $this->getApiResponse($response, [], 404);
        }
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
        // Get Parser Body
        $data = $request->getParsedBody();

        // Check is content api enabled
        if (! registry()->get('flextype.settings.api.content.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($data['id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is new_id param exists
        if (! isset($data['new_id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (! password_verify($data['access_token'], $tokenData['hashed_access_token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Move content
        content()->move($data['id'], $data['new_id']);

        // Fetch content
        $contentData = content()->fetch($data['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        } else {
            return $this->getApiResponse($response, [], 404);
        }
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
        // Get Parser Body
        $data = $request->getParsedBody();

        // Check is content api enabled
        if (! registry()->get('flextype.settings.api.content.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($data['id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is new_id param exists
        if (! isset($data['new_id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (! password_verify($data['access_token'], $tokenData['hashed_access_token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Copy content
        content()->copy($data['id'], $data['new_id']);

        // Fetch content
        $contentData = content()->fetch($data['new_id'])->toArray();

        // Return response
        if (count($contentData) > 0) {
            return $this->getApiResponse($response, $contentData, 200);
        } else {
            return $this->getApiResponse($response, [], 404);
        }
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
        // Get Parser Body
        $data = $request->getParsedBody();

        // Check is content api enabled
        if (! registry()->get('flextype.settings.api.content.enabled')) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token param exists
        if (! isset($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is id param exists
        if (! isset($data['id'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Check is token exists
        if (! tokens()->has($data['token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Fetch token
        $tokenData = tokens()->fetch($data['token']);

        // Verify access token
        if (! password_verify($data['access_token'], $tokenData['hashed_access_token'])) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(401), 401);
        }
        
        // Check token state and limit_calls
        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage(400), 400);
        }

        // Update token calls
        tokens()->update($data['token'], ['calls' => $tokenData['calls'] + 1]);

        // Copy content
        content()->delete($data['id']);

        // Return success response
        return $this->getApiResponse($response, [], 204);
    }
}
