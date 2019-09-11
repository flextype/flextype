<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Twig_Extension;
use Twig_SimpleFunction;
use function basename;
use function strrchr;
use function substr;

class FilesystemTwigExtension extends Twig_Extension
{
    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('filesystem_list_contents', [$this, 'list_contents']),
            new Twig_SimpleFunction('filesystem_has', [$this, 'has']),
            new Twig_SimpleFunction('filesystem_read', [$this, 'read']),
            new Twig_SimpleFunction('filesystem_ext', [$this, 'ext']),
            new Twig_SimpleFunction('filesystem_basename', [$this, 'basename']),
        ];
    }

    public function list_contents(string $directory = '', bool $recursive = false)
    {
        return Filesystem::listContents($directory, $recursive);
    }

    public function has($path)
    {
        return Filesystem::has($path);
    }

    public function read($path)
    {
        return Filesystem::read($path);
    }

    public function ext($file)
    {
        return substr(strrchr($file, '.'), 1);
    }

    public function basename($value, $suffix = '')
    {
        return basename($value, $suffix);
    }
}
