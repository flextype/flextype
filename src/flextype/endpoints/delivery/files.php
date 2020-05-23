<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_replace_recursive;

/**
 * API sys messages
 */
$api_sys_messages['AccessTokenInvalid'] = ['sys' => ['type' => 'Error', 'id' => 'AccessTokenInvalid'], 'message' => 'The access token you sent could not be found or is invalid.'];
$api_sys_messages['NotFound'] = ['sys' => ['type' => 'Error', 'id' => 'NotFound'], 'message' => 'The resource could not be found.'];

/**
 * Validate delivery files token
 */
function validate_delivery_files_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/delivery/files/' . $token . '/token.yaml');
}
