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

use Flextype\Endpoints\Registry;

use function app;

/**
 * Get registry item
 *
 * endpoint: GET /api/v0/registry
 *
 * Query:
 * id      - [REQUIRED] - Unique identifier of the registry item.
 * token   - [REQUIRED] - Valid public token.
 * default - [OPTIONAL] - Default value for registry item.
 *
 * Returns:
 * An array of registry objects.
 */
app()->get('/api/v0/registry', [Registry::class, 'get'])->setName('registry.get');
