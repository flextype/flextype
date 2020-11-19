<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

/**
 * Validate access token
 */
function validate_access_token($token) : bool
{
    return flextype('filesystem')->exists(PATH['project'] . '/tokens/access/' . $token . '/token.yaml');
}
