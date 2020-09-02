<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Symfony\Component\Finder\Finder;

if (! function_exists('find')) {
    /**
     * Create a Finder instance.
     */
    function find() : Finder
    {
        return new Finder();
    }
}

if (! function_exists('find_filter')) {
    /**
     * Create a Finder filter instance.
     */
    function find_filter(string $path, array $filter = [], $search_in = 'files')
    {
        $find = find()->in($path);

        isset($filter['depth']) and $find->depth($filter['depth']) or $find->depth(1);
        isset($filter['date']) and $find->date($filter['date']);
        isset($filter['size']) and $find->size($filter['size']);
        isset($filter['exclude']) and $find->exclude($filter['exclude']);
        isset($filter['contains']) and $find->contains($filter['contains']);
        isset($filter['not_contains']) and $find->notContains($filter['not_contains']);
        isset($filter['filter']) and $find->filter($filter['filter']);
        isset($filter['sort']) and $find->sort($filter['sort']);
        isset($filter['path']) and $find->path($filter['path']);
        isset($filter['sort_by']) && $filter['sort_by'] === 'atime' and $find->sortByAccessedTime();
        isset($filter['sort_by']) && $filter['sort_by'] === 'mtime' and $find->sortByModifiedTime();
        isset($filter['sort_by']) && $filter['sort_by'] === 'ctime' and $find->sortByChangedTime();

        return $search_in === 'files' ? $find->files() : $find->directories();
    }
}
