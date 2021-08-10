<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Endpoints;

use Psr\Http\Message\ResponseInterface;

use function count;
use function registry;
use function serializers;

class Endpoints
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
        ],
        401 => [
            'title' => 'Unauthorized',
            'message' => 'Token is wrong',
        ],
        404 => [
            'title' => 'Not Found',
            'message' => 'Not Found',
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
     * Get API responce
     *
     * @param ResponseInterface $response PSR7 response.
     * @param array             $body     Response body.
     * @param int               $status   Status code.
     *
     * @return ResponseInterface Response.
     */
    public function getApiResponse(ResponseInterface $response, array $body = [], int $status = 200): ResponseInterface
    {
        if (count($body) > 0) {
            $response->getBody()->write(serializers()->json()->encode($body));
        }

        $response->withStatus($status);
        $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));

        return $response;
    }
}
