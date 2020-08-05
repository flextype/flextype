<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

/**
 * API errors
 */
$api_errors = [
    '0000' => [
        'http_status_code' => 500,
        'message' => 'Internal Error',
    ],
    '0001' => [
        'http_status_code' => 404,
        'message' => 'Not Found',
    ],
    '0002' => [
        'http_status_code' => 400,
        'message' => 'Bad Request',
    ],
    '0003' => [
        'http_status_code' => 401,
        'message' => 'Unauthorized',
    ],
    '0100' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0101' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0102' => [
        'http_status_code' => 404,
        'message' => 'Entry not found',
    ],
    '0200' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0201' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0202' => [
        'http_status_code' => 404,
        'message' => 'Config item not found',
    ],
    '0300' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0301' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0302' => [
        'http_status_code' => 404,
        'message' => 'Registry item not found',
    ],
    '0400' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0401' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0402' => [
        'http_status_code' => 404,
        'message' => 'Image not found',
    ],
    '0500' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0501' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0502' => [
        'http_status_code' => 404,
        'message' => 'File not found',
    ],
    '0600' => [
        'http_status_code' => 400,
        'message' => 'Wrong query params or not defined',
    ],
    '0601' => [
        'http_status_code' => 400,
        'message' => 'Wrong body params or not defined',
    ],
    '0602' => [
        'http_status_code' => 404,
        'message' => 'Folder not found',
    ],
];


$api_sys_messages['AccessTokenInvalid'] = ['sys' => ['type' => 'Error', 'id' => 'AccessTokenInvalid'], 'message' => 'The access token you sent could not be found or is invalid.'];
$api_sys_messages['NotFound']           = ['sys' => ['type' => 'Error', 'id' => 'NotFound'], 'message' => 'The resource could not be found.'];
