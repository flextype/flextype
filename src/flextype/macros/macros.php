<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;
use Atomastic\Macroable\Macroable;

if (! Arrays::hasMacro('onlyFromCollection')) {
    /**
     * Return slice of an array with just a given keys.
     *
     * @param array $keys List of keys to return.
     *
     * @return Arrays Returns instance of The Arrays class.
     */
    Arrays::macro('onlyFromCollection', function(array $keys) {
        $result = [];

        foreach ($this->toArray() as $key => $value) {
            $result[$key] = arrays($value)->only($keys)->toArray();
        }

        return arrays($result);
    });
}

if (! Arrays::hasMacro('exceptFromCollection')) {
    /**
     * Return slice of an array except given keys.
     *
     * @param array $keys List of keys to except.
     *
     * @return Arrays Returns instance of The Arrays class.
     */
    Arrays::macro('exceptFromCollection', function(array $keys) {
        $result = [];

        foreach ($this->toArray() as $key => $value) {
            $result[$key] = arrays($value)->except($keys)->toArray();
        }

        return arrays($result);
    });
}