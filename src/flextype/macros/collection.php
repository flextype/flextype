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

use Glowy\Arrays\Arrays as Collection;

use function Glowy\Arrays\arrays as collection;

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
