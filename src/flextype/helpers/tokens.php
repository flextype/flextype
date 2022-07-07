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

namespace Flextype;

use function bin2hex;
use function function_exists;
use function password_hash;
use function password_verify;
use function random_bytes;

use const PASSWORD_BCRYPT;

if (! function_exists('generateToken')) {
    /**
     * Generate token.
     *
     * @param int $length Token string length.
     *
     * @return string Token string.
     */
    function generateToken(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }
}

if (! function_exists('generateTokenHash')) {
    /**
     * Generate token hash.
     *
     * @return string Token string hashed.
     */
    function generateTokenHash(string $token): string
    {
        return password_hash($token, PASSWORD_BCRYPT);
    }
}

if (! function_exists('verifyTokenHash')) {
    /**
     * Verify token hash.
     *
     * @param string $token       Token.
     * @param string $tokenHashed Token hash.
     *
     * @return bool Token string.
     */
    function verifyTokenHash(string $token, string $tokenHashed): bool
    {
        return password_verify($token, $tokenHashed);
    }
}
