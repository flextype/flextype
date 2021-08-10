<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Utils;

/**
 * Clear cache
 *
 * endpoint: POST /api/utils/cache/clear
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->post('/api/utils/cache/clear', [Utils::class, 'clearCache']);