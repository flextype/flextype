<?php

declare(strict_types=1);

use Glowy\Arrays\Arrays as Collection;

if (! Collection::hasMacro('onlyFromCollection')) {
    /**
     * Return slice of an array with just a given keys.
     *
     * @param array $keys List of keys to return.
     *
     * @return Arrays Returns instance of The Arrays class.
     */
    Collection::macro('onlyFromCollection', function (array $keys) {
        $result = [];

        foreach ($this->toArray() as $key => $value) {
            $result[$key] = collection($value)->only($keys)->toArray();
        }

        return collection($result);
    });
}

if (! Collection::hasMacro('exceptFromCollection')) {
    /**
     * Return slice of an array except given keys.
     *
     * @param array $keys List of keys to except.
     *
     * @return Arrays Returns instance of The Arrays class.
     */
    Collection::macro('exceptFromCollection', function (array $keys) {
        $result = [];

        foreach ($this->toArray() as $key => $value) {
            $result[$key] = collection($value)->except($keys)->toArray();
        }

        return collection($result);
    });
}
