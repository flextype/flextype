<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

/**
 * Validate access token
 */
function validate_access_token($token) : bool
{
    return Filesystem::has(PATH['project'] . '/tokens/access/' . $token . '/token.yaml');
}
