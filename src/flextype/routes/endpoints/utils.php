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
