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

class GlobalVarsTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    public function getGlobals()
    {
        return [
            'PATH_SITE' => PATH['site'],
            'PATH_PLUGINS' => PATH['plugins'],
            'PATH_THEMES' => PATH['themes'],
            'PATH_ENTRIES' => PATH['entries'],
            'PATH_SNIPPETS' => PATH['snippets'],
            'PATH_CONFIG_DEFAULT' => PATH['config']['default'],
            'PATH_CONFIG_SITE' => PATH['config']['site'],
            'PATH_CACHE' => PATH['cache'],
            'flextype_version' => FLEXTYPE_VERSION,
            'registry' => $this->flextype['registry']->dump()
        ];
    }
}
