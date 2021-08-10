<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Images;

/**
 * Fetch image
 *
 * endpoint: GET /api/images
 *
 * Parameters:
 * path - [REQUIRED] - The file path with valid params for image manipulations.
 *
 * Query:
 * token - [REQUIRED] - Valid public token.
 *
 * Returns:
 * Image file
 */
app()->get('/api/images/{path:.+}', [Images::class, 'fetch']);