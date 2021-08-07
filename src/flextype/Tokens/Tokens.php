<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Tokens;

use Atomastic\Macroable\Macroable;
use Flextype\Entries;
use Exception;

class Tokens extends Entries
{
    use Macroable;

    public function create(string $id, array $data = []): bool
    {
        throw new Exception('Use generate method instead');
        return false;
    }

    public function generate(array $data = []): string
    {
        $id = strings()->random()->toString();

        parent::create($id, $data);

        return $id;
    }
}