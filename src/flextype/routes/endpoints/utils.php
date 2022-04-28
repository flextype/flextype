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

use Flextype\Endpoints\Utils;

use function app;

/**
 * Generate token
 *
 * endpoint: POST /api/v0/utils/generate-token
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * 
 * Returns:
 * Generated token object.
 */
app()->post('/api/v0/utils/generate-token', [Utils::class, 'generateToken'])->setName('utils.generate-token');

/**
 * Generate token hash
 *
 * endpoint: POST /api/v0/utils/generate-token-hash
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * string       - [REQUIRED] - String to hash.
 * 
 * Returns:
 * Generated token hash object.
 */
app()->post('/api/v0/utils/generate-token-hash', [Utils::class, 'generateTokenHash'])->setName('utils.generate-token-hash');

/**
 * Verify token hash
 *
 * endpoint: POST /api/v0/utils/verify-token-hash
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * string       - [REQUIRED] - String to verify.
 * hash         - [REQUIRED] - Hash to verify.
 * 
 * Returns:
 * Token verification object.
 */
app()->post('/api/v0/utils/verify-token-hash', [Utils::class, 'verifyTokenHash'])->setName('utils.verify-token-hash');
