<?php 

declare(strict_types=1);

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

if (! function_exists('tokenHash')) {
    /**
     * Generate token hash.
     *
     * @param int $length Token string length.
     * 
     * @return string Token string.
     */
    function generateTokenHash(int $length = 16): string
    {
        return password_hash(generateToken($length), PASSWORD_BCRYPT);
    }
}

if (! function_exists('validateTokenHash')) {
    /**
     * Validate token hash.
     *
     * @param string $token       Token.
     * @param string $tokenHashed Token hash.
     * 
     * @return bool Token string.
     */
    function validateTokenHash(string $token, string $tokenHashed): bool
    {
        return password_verify($token, $tokenHashed);
    }
}
