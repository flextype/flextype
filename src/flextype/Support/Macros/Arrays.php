<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;

/**
 * Create a new Arrays macro for 
 *
 * @param  mixed $items   Items.
 * @param  array $options Options array.
 *
 * @return array
 */
Arrays::macro('onlyFromCollection', function(array $keys) {
    $result = [];

    foreach ($this->toArray() as $key => $value) {
        $result[$key] = arrays($value)->only($keys)->toArray();
    }

    return arrays($result);
});

Arrays::macro('exceptFromCollection', function(array $keys) {
    $result = [];

    foreach ($this->toArray() as $key => $value) {
        $result[$key] = arrays($value)->except($keys)->toArray();
    }

    return arrays($result);
});