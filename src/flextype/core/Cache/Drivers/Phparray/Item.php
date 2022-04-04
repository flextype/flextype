<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Phpfastcache\Drivers\Phparray;

use Phpfastcache\Core\Item\{ExtendedCacheItemInterface, ItemBaseTrait};
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Drivers\Phparray\Driver as PhparrayDriver;
use Phpfastcache\Exceptions\{PhpfastcacheInvalidArgumentException};

/**
 * Class Item
 * @package phpFastCache\Drivers\Phparray
 */
class Item implements ExtendedCacheItemInterface
{
    use ItemBaseTrait {
        ItemBaseTrait::__construct as __BaseConstruct;
    }

    /**
     * Item constructor.
     * @param Driver $driver
     * @param $key
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function __construct(PhparrayDriver $driver, $key)
    {
        $this->__BaseConstruct($driver, $key);
    }

    /**
     * @param ExtendedCacheItemPoolInterface $driver
     * @return static
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function setDriver(ExtendedCacheItemPoolInterface $driver)
    {
        if ($driver instanceof PhparrayDriver) {
            $this->driver = $driver;

            return $this;
        }

        throw new PhpfastcacheInvalidArgumentException('Invalid driver instance');
    }
}