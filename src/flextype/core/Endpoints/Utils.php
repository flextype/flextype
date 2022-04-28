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

use function count;
use function filesystem;

class Utils extends Api
{
    /**
     * Generate token
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function generateToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

        // Generate token
        $token = generateToken();

        // Return success response
        return $this->getApiResponse($response, ['token' => $token], 200);
    }

    /**
     * Generate token hash
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function generateTokenHash(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();
        
        // Validate Api Request
       if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'utils',
                'params' => ['token', 'access_token', 'string'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Generate token
        $token = generateTokenHash($requestParsedBody['string']);

        // Return success response
        return $this->getApiResponse($response, ['hashed_token' => $token], 200);
    }

    /**
     * Verify token hash
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function verifyTokenHash(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();
        
        // Validate Api Request
       if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'utils',
                'params' => ['token', 'access_token', 'string', 'hash'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        // Verify
        $verified = verifyTokenHash($requestParsedBody['string'], $requestParsedBody['hash']);

        // Return success response
        return $this->getApiResponse($response, ['verified' => $verified], 200);
    }

    /**
     * Create token
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function createToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

        $token               = generateToken();
        $access_token        = generateToken();
        $hashed_access_token = generateTokenHash($access_token);

        ! entries()->has('tokens') and entries()->create('tokens', ['title' => 'Tokens']);

        // Create new entry
        entries()->create('tokens/' . $token, ['hashed_access_token' => $hashed_access_token]);

        // Fetch entry
        $entryData = entries()->fetch('tokens/' . $token)->toArray();

        // Return response
        if (count($entryData) > 0) {
            return $this->getApiResponse($response, ['token' => $token, 'access_token' => $access_token], 200);
        }

        return $this->getApiResponse($response, [], 404);
    }
}
