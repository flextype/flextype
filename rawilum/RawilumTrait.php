<?php namespace Rawilum;

/**
 * @package Rawilum
 *
 * @author Sergey Romanenko <awilum@yandex.ru>
 * @link http://rawilum.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

trait RawilumTrait
{
    /**
     * @var Rawilum
     */
    protected static $rawilum;

    /**
     * @return Rawilum
     */
    public static function getRawilum()
    {
        if (!self::$rawilum) {
            self::$rawilum = Rawilum::instance();
        }

        return self::$rawilum;
    }

    /**
     * @param Rawilum $rawilum
     */
    public static function setRawilum(Rawilum $rawilum)
    {
        self::$rawilum = $rawilum;
    }
}
