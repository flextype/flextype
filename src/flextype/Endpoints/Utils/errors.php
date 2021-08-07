<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

function getApiResponseErrors(): array
{
    return [
        '400' => [
            'http_status_code' => 400,
            'title' => 'Bad Request',
            'message' => 'Validation for this particular item failed',
        ],
        '401' => [
            'http_status_code' => 401,
            'title' => 'Unauthorized',
            'message' => 'Token is wrong',
        ],
        '404' => [
            'http_status_code' => 404,
            'title' => 'Not Found',
            'message' => 'Not Found',
        ],
    ];
}

function getApiResponseWithError($response, $code)
{
    $response->getBody()->write(serializers()->json()->encode(getApiResponseErrors()[$code]));
    $response->withStatus(getApiResponseErrors()[$code]['http_status_code']);
    $response->withHeader('Content-Type', 'application/json;charset=' . registry()->get('flextype.settings.charset'));
    return $response;
}