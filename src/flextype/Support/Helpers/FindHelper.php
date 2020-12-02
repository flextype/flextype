<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Symfony\Component\Finder\Finder;

if (! function_exists('find')) {
     /**
      * Create a Finder instance with predefined filter params or without them.
      *
      * @param  string $path      Path.
      * @param  array  $params    Parameters array.
      * @param  string $search_in Search in 'files' or 'directories'. Default is 'files'.
      *
      * @return Symfony\Component\Finder<Finder>
      */
    function find(string $path = '', array $params = [], string $search_in = 'files'): Finder
    {
        $find = filesystem()->find()->in($path);

        isset($params['depth']) and $find->depth($params['depth']) or $find->depth(1);
        isset($params['date']) and $find->date($params['date']);
        isset($params['size']) and $find->size($params['size']);
        isset($params['exclude']) and $find->exclude($params['exclude']);
        isset($params['contains']) and $find->contains($params['contains']);
        isset($params['not_contains']) and $find->notContains($params['not_contains']);
        isset($params['filter']) and $find->filter($params['filter']);
        isset($params['sort']) and $find->sort($params['sort']);
        isset($params['path']) and $find->path($params['path']);
        isset($params['sort_by']) && $params['sort_by'] === 'atime' and $find->sortByAccessedTime();
        isset($params['sort_by']) && $params['sort_by'] === 'mtime' and $find->sortByModifiedTime();
        isset($params['sort_by']) && $params['sort_by'] === 'ctime' and $find->sortByChangedTime();

        return $search_in === 'directories' ? $find->directories() : $find->files();
    }
}
