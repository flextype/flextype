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

use Flextype\Endpoints\Tokens;

use function app;

/**
 * Generate token
 *
 * endpoint: POST /api/v1/tokens/generate
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * 
 * Returns:
 * Generated token object.
 */
app()->post('/api/v1/tokens/generate', [Tokens::class, 'generate'])->setName('tokens.generate');

/**
 * Generate token hash
 *
 * endpoint: POST /api/v1/tokens/generate-hash
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * string       - [REQUIRED] - String to hash.
 * 
 * Returns:
 * Generated token hash object.
 */
app()->post('/api/v1/tokens/generate-hash', [Tokens::class, 'generateHash'])->setName('tokens.generate-hash');

/**
 * Verify token hash
 *
 * endpoint: POST /api/v1/tokens/verify-hash
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
app()->post('/api/v1/tokens/verify-hash', [Tokens::class, 'verifyHash'])->setName('tokens.verify-hash');

/**
 * Create token entry
 *
 * endpoint: POST /api/v1/tokens
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 * data         - [OPTIONAL] - Data to store for the token.
 *
 * Returns:
 * Token entry object with the token entry that was just created.
 */
app()->post('/api/v1/tokens', [Tokens::class, 'create'])->setName('tokens.create');

/**
 * Update token entry
 *
 * endpoint: PATCH /api/v1/tokens
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the token entry.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid private access token.
 * data          - [REQUIRED] - Data to update for the token entry.
 *
 * Returns:
 * Toeken entry object for the entry that was just updated.
 */
app()->patch('/api/v1/tokens', [Tokens::class, 'update'])->setName('tokens.update');

/**
 * Delete token entry
 *
 * endpoint: DELETE /api/v1/tokens
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the token entry.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid private access token.
 *
 * Returns:
 * Empty body with HTTP status 204
 */
app()->delete('/api/v1/tokens', [Tokens::class, 'delete'])->setName('tokens.delete');

/**
 * Fetch token entry
 *
 * endpoint: GET /api/v1/tokens
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the token entry.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * Entry object.
 */
app()->get('/api/v1/tokens', [Tokens::class, 'fetch'])->setName('tokens.fetch');
