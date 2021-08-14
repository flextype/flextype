<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Content;

use function app;

/**
 * Fetch content
 *
 * endpoint: GET /api/content
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the content.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of content objects.
 */
app()->get('/api/content', [Content::class, 'fetch']);

/**
 * Create content
 *
 * endpoint: POST /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to store for the content.
 *
 * Returns:
 * Returns the content object for the content that was just created.
 */
app()->post('/api/content', [Content::class, 'create']);

/**
 * Update content
 *
 * endpoint: PATCH /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to update for the content.
 *
 * Returns:
 * Returns the content object for the content that was just updated.
 */
app()->patch('/api/content', [Content::class, 'update']);

/**
 * Move content
 *
 * endpoint: PUT /api/content
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * new_id        - [REQUIRED] - New Unique identifier of the content.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the content object for the content that was just moved.
 */
app()->put('/api/content', [Content::class, 'move']);

/**
 * Copy content
 *
 * endpoint: PUT /api/content/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the content.
 * new_id        - [REQUIRED] - New Unique identifier of the content.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the content object for the content that was just copied.
 */
app()->put('/api/content/copy', [Content::class, 'copy']);

/**
 * Delete content
 *
 * endpoint: DELETE /api/content
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the content.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->delete('/api/content', [Content::class, 'delete']);
