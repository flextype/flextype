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

use Flextype\Endpoints\Cache;

/**
 * Clear cache.
 *
 * endpoint: POST /api/v1/cache/clear
 *
 * Body:
 * token        - [REQUIRED] - Valid public token.
 * access_token - [REQUIRED] - Valid private access token.
 *
 * Returns:
 * Empty body with HTTP status 204
 */
app()->post('/api/v1/cache/clear', [Cache::class, 'clear'])->setName('cache.clear');
