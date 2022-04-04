<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Endpoints\Utils;

use function app;

/**
 * Clear cache
 *
 * endpoint: POST /api/v0/utils/cache/clear
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid access token.
 *
 * Returns:
 * Returns an empty body with HTTP status 204
 */
app()->post('/api/v0/utils/cache/clear', [Utils::class, 'clearCache']);
