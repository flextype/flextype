<?php 

declare(strict_types=1);

namespace Flextype;

if (! function_exists('generateToken')) {
    /**
     * Generate token.
     *
     * @param int $length Token string length.
     * 
     * @return strings Token string.
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
     * @return strings Token string.
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
