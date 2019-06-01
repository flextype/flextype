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

use Flextype\Component\Session\Session;

class GlobalVarsAdminTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
            'is_logged' => ((Session::exists('role') && Session::get('role') == 'admin') ? true : false),
            'username' => Session::exists('username') ? Session::get('username') : ''
        ];
    }
}
