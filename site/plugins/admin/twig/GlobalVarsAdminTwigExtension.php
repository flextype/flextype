<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Session\Session;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;

class GlobalVarsAdminTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
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

    /**
     * Register Global variables in an extension
     */
    public function getGlobals()
    {
        return [
            'is_logged' => (Session::exists('role') && Session::get('role') === 'admin'),
            'username' => Session::exists('username') ? Session::get('username') : '',
            'rolename' => Session::exists('role') ? Session::get('role') : '',
        ];
    }
}
