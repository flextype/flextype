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

use Symfony\Component\Finder\Finder;

use function function_exists;
use function Glowy\Filesystem\filesystem;

if (! function_exists('find')) {
    /**
     * Create a Finder instance with predefined filter params or without them.
     *
     * @param  string $path     Path.
     * @param  array  $options  Options array.
     * @param  string $searchIn Search in 'files' or 'directories'. Default is 'files'.
     *
     * @return Finder Finder instance.
     */
    function find(string $path = '', array $options = [], string $searchIn = 'files'): Finder
    {
        $find = filesystem()->find()->in($path);

        isset($options['depth']) ? $find->depth($options['depth']) : $find->depth(1);
        isset($options['date']) and $find->date($options['date']);
        isset($options['size']) and $find->size($options['size']);
        isset($options['exclude']) and $find->exclude($options['exclude']);
        isset($options['contains']) and $find->contains($options['contains']);
        isset($options['not_contains']) and $find->notContains($options['not_contains']);
        isset($options['filter']) and $find->filter($options['filter']);
        isset($options['sort']) and $find->sort($options['sort']);
        isset($options['path']) and $find->path($options['path']);
        isset($options['sort_by']) && $options['sort_by'] === 'atime' and $find->sortByAccessedTime();
        isset($options['sort_by']) && $options['sort_by'] === 'mtime' and $find->sortByModifiedTime();
        isset($options['sort_by']) && $options['sort_by'] === 'ctime' and $find->sortByChangedTime();

        return $searchIn === 'directories' ? $find->directories() : $find->files();
    }
}
