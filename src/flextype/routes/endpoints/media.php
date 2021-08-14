<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Content;

/**
 * Fetch media
 *
 * endpoint: GET /api/media
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the media.
 * token   - [REQUIRED] - Valid public token.
 * options - [OPTIONAL] - Select items in collection by given conditions.
 *
 * Returns:
 * An array of media objects.
 */
app()->get('/api/media', [Content::class, 'fetch']);

/**
 * Create media
 *
 * endpoint: POST /api/media
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the media.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to store for the media.
 *
 * Returns:
 * Returns the media object for the media that was just created.
 */
app()->post('/api/media', [Content::class, 'create']);

/**
 * Update media
 *
 * endpoint: PATCH /api/media
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the media.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 * data          - [REQUIRED] - Data to update for the media.
 *
 * Returns:
 * Returns the media object for the media that was just updated.
 */
app()->patch('/api/media', [Content::class, 'update']);

/**
 * Move media
 *
 * endpoint: PUT /api/media
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the media.
 * new_id        - [REQUIRED] - New Unique identifier of the media.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the media object for the media that was just moved.
 */
app()->put('/api/media', [Content::class, 'move']);

/**
 * Copy media
 *
 * endpoint: PUT /api/media/copy
 *
 * Body:
 * id            - [REQUIRED] - Unique identifier of the media.
 * new_id        - [REQUIRED] - New Unique identifier of the media.
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns the media object for the media that was just copied.
 */
app()->put('/api/media/copy', [Content::class, 'copy']);

/**
 * Delete media
 *
 * endpoint: DELETE /api/media
 *
 * Body:
 * id           - [REQUIRED] - Unique identifier of the media.
 * token        - [REQUIRED] - Valid pulbic token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->delete('/api/media', [Content::class, 'delete']);
