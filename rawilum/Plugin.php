<?php
namespace Rawilum;

/**
 * This file is part of the Rawilum.
 *
 * (c) Romanenko Sergey / Awilum <awilum@msn.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Plugin
{
    /**
     * @var Rawilum
     */
    protected $rawilum;

    /**
     * __construct
     */
    public function __construct(Rawilum $c)
    {
        $this->rawilum = $c;
    }
}
