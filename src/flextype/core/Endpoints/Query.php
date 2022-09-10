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

use function Glowy\Strings\strings;
use function Flextype\parsers;
use function count;

class Query extends Api
{
    /**
     * Evaluate query.
     *
     * @param ServerRequestInterface $request  PSR7 request.
     * @param ResponseInterface      $response PSR7 response.
     *
     * @return ResponseInterface Response.
     */
    public function evaluate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Get Request Parsed Body
        $requestParsedBody = $request->getParsedBody();

        // Validate Api Request
        if (
            count($result = $this->validateApiRequest([
                'request' => $request,
                'api' => 'query',
                'params' => ['query', 'access_token', 'token'],
            ])) > 0
        ) {
            return $this->getApiResponse($response, $this->getStatusCodeMessage($result['http_status_code']), $result['http_status_code']);
        }

        $data = [];

        // Evaluate the queries
        foreach ($requestParsedBody['query'] as $key => $value) {
            $evaluatedValue = parsers()->expressions()->eval($value);
       
            if ($evaluatedValue instanceof \Glowy\Arrays\Arrays) {
                $evaluatedValue = $evaluatedValue->toArray();
            }

            if ($evaluatedValue instanceof \Glowy\Strings\Strings) {
                $evaluatedValue = $evaluatedValue->toString();
            }
        
            $data[$key] = $evaluatedValue;
        }

        $result = [];

        // Clean up data
        foreach ($data as $key => $value) {

            // Replace private fields
            if (strings($key)->startsWith('_')) {
                continue;
            }

            $result[$key] = $value;
        }

        // Return response
        if (count($result) > 0) {
            return $this->getApiResponse($response, $result, 200);
        }

        return $this->getApiResponse($response, [], 404);
    }
}
