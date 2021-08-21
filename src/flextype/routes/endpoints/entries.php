<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Entries;

use function app;

/**
 * Fetch entry
 *
 * endpoint: GET /api/entries
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the entry.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of entry objects.
 */
app()->get('/api/entries', [Entries::class, 'fetch']);

/**
 * Create entry
 *
 * endpoint: POST /api/entries
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * token         -
 *  [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to store for the entry.
 *
 * Returns:
 * Returns the entry object for the entry that was just created.
 */
app()->post('/api/entries', [Entries::class, 'create']);

/**
 * Update entry
 *
 * endpoint: PATCH /api/entries
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to update for the entry.
 *
 * Returns:
 * Returns the entry object for the entry that was just updated.
 */
app()->patch('/api/entries', [Entries::class, 'update']);

/**
 * Move entry
 *
 * endpoint: PUT /api/entries
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * new_id        - [REQUIRED] - New Unique identifier of the entry.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the entry object for the entry that was just moved.
 */
app()->put('/api/entries', [Entries::class, 'move']);

/**
 * Copy entry
 *
 * endpoint: PUT /api/entries/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * new_id        - [REQUIRED] - New Unique identifier of the entry.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the entry object for the entry that was just copied.
 */
app()->put('/api/entries/copy', [Entries::class, 'copy']);

/**
 * Delete entry
 *
 * endpoint: DELETE /api/entries
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the entry.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->delete('/api/entries', [Entries::class, 'delete']);
