<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;

use function array_keys;
use function array_merge;
use function count;
use function in_array;
use function is_string;
use function password_verify;
use function registry;
use function serializers;
use function tokens;

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
            'message' => 'Not Found',
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

        $response->withStatus($status);
        $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));

        return $response;
    }
}
