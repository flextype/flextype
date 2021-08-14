<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Tokens;

use function app;

/**
 * Fetch token
 *
 * endpoint: GET /api/tokens
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the token.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of token objects.
 */
app()->get('/api/tokens', [Tokens::class, 'fetch']);

/**
 * Create token
 *
 * endpoint: POST /api/tokens
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the token.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to store for the token.
 *
 * Returns:
 * Returns the token object for the token that was just created.
 */
app()->post('/api/tokens', [Tokens::class, 'create']);

/**
 * Update token
 *
 * endpoint: PATCH /api/tokens
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the token.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to update for the token.
 *
 * Returns:
 * Returns the token object for the token that was just updated.
 */
app()->patch('/api/tokens', [Tokens::class, 'update']);

/**
 * Move token
 *
 * endpoint: PUT /api/tokens
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the token.
 * new_id        - [REQUIRED] - New Unique identifier of the token.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the token object for the token that was just moved.
 */
app()->put('/api/tokens', [Tokens::class, 'move']);

/**
 * Copy token
 *
 * endpoint: PUT /api/tokens/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the token.
 * new_id        - [REQUIRED] - New Unique identifier of the token.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the token object for the token that was just copied.
 */
app()->put('/api/tokens/copy', [Tokens::class, 'copy']);

/**
 * Delete token
 *
 * endpoint: DELETE /api/tokens
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the token.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->delete('/api/tokens', [Tokens::class, 'delete']);
