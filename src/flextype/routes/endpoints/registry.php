<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
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
 * key     - [REQUIRED] - Unique identifier of the registry item key.
 * token   - [REQUIRED] - Valid public token.
 * default - [OPTIONAL] - Default value for registry item key.
 *
 * Returns:
 * An array of registry objects.
 */
app()->get('/api/v0/registry', [Registry::class, 'get']);
