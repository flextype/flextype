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

use Flextype\Endpoints\Entries;

use function app;

/**
 * Fetch entry
 *
 * endpoint: GET /api/v0/entries
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the entry.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of entry objects.
 */
app()->get('/api/v0/entries', [Entries::class, 'fetch']);

/**
 * Create entry
 *
 * endpoint: POST /api/v0/entries
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the entry.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to store for the entry.
 *
 * Returns:
 * Returns the entry object for the entry that was just created.
 */
app()->post('/api/v0/entries', [Entries::class, 'create']);

/**
 * Update entry
 *
 * endpoint: PATCH /api/v0/entries
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
app()->patch('/api/v0/entries', [Entries::class, 'update']);

/**
 * Move entry
 *
 * endpoint: PUT /api/v0/entries
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
app()->put('/api/v0/entries', [Entries::class, 'move']);

/**
 * Copy entry
 *
 * endpoint: PUT /api/v0/entries/copy
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
app()->put('/api/v0/entries/copy', [Entries::class, 'copy']);

/**
 * Delete entry
 *
 * endpoint: DELETE /api/v0/entries
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the entry.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->delete('/api/v0/entries', [Entries::class, 'delete']);
