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

use Flextype\Endpoints\Query;

/**
 * Create entry
 *
 * endpoint: POST /api/v1/query
 *
 * Body:
 * token         - [REQUIRED] - Valid public token.
 * access_token  - [REQUIRED] - Valid private access token.
 * query         - [REQUIRED] - Array of queries.
 *
 * Returns:
 * Query object.
 */
app()->post('/api/v1/query', [Query::class, 'evaluate'])->setName('query.evaluate');