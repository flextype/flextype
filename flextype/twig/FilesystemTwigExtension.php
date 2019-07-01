<?php

/**
 * @package Flextype
 *
 * @author Sergey Romanenko <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;

class FilesystemTwigExtension extends \Twig_Extension
{
    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('filesystem_get_files_list', [$this, 'get_files_list']),
            new \Twig_SimpleFunction('filesystem_has', [$this, 'has']),
            new \Twig_SimpleFunction('filesystem_read', [$this, 'read']),
            new \Twig_SimpleFunction('filesystem_ext', [$this, 'ext']),
            new \Twig_SimpleFunction('filesystem_basename', [$this, 'basename']),
        ];
    }

    public function get_files_list(string $folder, $type = null, bool $file_path = true, bool $multilevel = true)
    {
        return Filesystem::getFilesList($folder, $type, $file_path, $multilevel);
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
