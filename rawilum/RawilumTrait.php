<?php
namespace Rawilum;

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
