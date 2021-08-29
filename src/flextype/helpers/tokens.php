<?php 

declare(strict_types=1);

if (! function_exists('token')) {
    /**
     * Token.
     *
     * @param int $length Token string length.
     * 
     * @return strings Token string.
     */
    function token(int $length = 16): string
    {
        return bin2hex(random_bytes($length));
    }
}

if (! function_exists('tokenHash')) {
    /**
     * Token hash.
     *
     * @param int $length Token string length.
     * 
     * @return string Token string.
     */
    function tokenHash(int $length = 16): string
    {
        return password_hash(token($length), PASSWORD_BCRYPT);
    }
}

if (! function_exists('tokenHashValidate')) {
    /**
     * Token hash validate.
     *
     * @param string $token       Token string length.
     * @param string $tokenHashed Token string length.
     * 
     * @return bool Token string.
     */
    function tokenHashValidate(string $token, string $tokenHashed): bool
    {
        return password_verify($token, $tokenHashed);
    }
}
