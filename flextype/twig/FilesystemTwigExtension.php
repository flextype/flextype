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
            new \Twig_SimpleFunction('filesystem_has', array($this, 'filesystem_has')),
            new \Twig_SimpleFunction('filesystem_read', array($this, 'filesystem_read')),
        ];
    }

    public function has($path)
    {
        return Filesystem::has($path);
    }

    public function read($path)
    {
        return Filesystem::read($path);
    }
}
