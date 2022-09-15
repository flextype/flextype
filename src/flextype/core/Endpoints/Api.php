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

use function array_keys;
use function array_merge;
use function count;
use function Flextype\entries;
use function Flextype\registry;
use function Flextype\serializers;
use function Flextype\verifyTokenHash;
use function in_array;
use function is_string;

class Api
{
    /**
     * Status code messages.
     *
     * @var array
     * @access public
     */
    private array $statusCodeMessages = [
        400 => [
            'title' => 'Bad Request',
            'message' => 'Validation for this particular item failed',
            'http_status_code' => 400,
        ],
        401 => [
            'title' => 'Unauthorized',
            'message' => 'Token is wrong',
            'http_status_code' => 401,
        ],
        404 => [
            'title' => 'Not Found',
            'message' => 'The requested resource or endpoint could not be found',
            'http_status_code' => 404,
        ],
    ];

    /**
     * Get Status Code Message.
     *
     * @param int $status Status Code.
     *
     * @return array Message.
     */
    public function getStatusCodeMessage(int $status): array
    {
        return $this->statusCodeMessages[$status];
    }

    /**
     * Validate Api Request.
     */
    public function validateApiRequest(array $options)
    {
        if (! isset($options['api']) && ! is_string($options['api'])) {
            return $this->getStatusCodeMessage(400);
        }

        if (! isset($options['request'])) {
            return $this->getStatusCodeMessage(400);
        }

        if (! isset($options['params'])) {
            return $this->getStatusCodeMessage(400);
        }

        $queryData = $options['request']->getQueryParams() ?? [];
        $bodyData  = $options['request']->getParsedBody() ?? [];

        $data = array_merge($queryData, $bodyData);

        $dataTest = true;
        foreach ($options['params'] as $key => $value) {
            if (in_array($value, array_keys($data))) {
                continue;
            }

            $dataTest = false;
        }

        if (! $dataTest) {
            return $this->getStatusCodeMessage(400);
        }

        // Check is api enabled
        if (! registry()->get('flextype.settings.api.' . $options['api'] . '.enabled')) {
            return $this->getStatusCodeMessage(400);
        }

        if (! entries()->has('tokens/' . $data['token'])) {
            return $this->getStatusCodeMessage(401);
        }

        // Fetch token
        $tokenData = entries()->fetch('tokens/' . $data['token']);

        if (
            ! isset($tokenData['state']) ||
            ! isset($tokenData['limit_calls']) ||
            ! isset($tokenData['calls'])
        ) {
            return $this->getStatusCodeMessage(400);
        }

        if (
            $tokenData['state'] === 'disabled' ||
            ($tokenData['limit_calls'] !== 0 && $tokenData['calls'] >= $tokenData['limit_calls'])
        ) {
            return $this->getStatusCodeMessage(400);
        }

        if (isset($data['access_token'])) {
            if (! isset($tokenData['hashed_access_token'])) {
                return $this->getStatusCodeMessage(401);
            }

            if (! verifyTokenHash($data['access_token'], $tokenData['hashed_access_token'])) {
                return $this->getStatusCodeMessage(401);
            }
        }

        // Update token calls
        entries()->update('tokens/' . $data['token'], ['calls' => $tokenData['calls'] + 1]);

        return [];
    }

    /**
     * Get API response.
     *
     * @param array $body   Response body.
     * @param int   $status Status code.
     *
     * @return ResponseInterface Response.
     */
    public function getApiResponse($response, array $body = [], int $status = 200): ResponseInterface
    {
        if (count($body) > 0) {
            $response->getBody()->write(serializers()->json()->encode($body));
        }

        $response = $response->withStatus($status);
        $response = $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));

        return $response;
    }
}
