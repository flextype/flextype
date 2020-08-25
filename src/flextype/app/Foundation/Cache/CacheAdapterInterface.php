<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

interface CacheAdapterInterface
{
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct();

    /**
     * Returns the cache driver object
     */
    public function getDriver() : object;
}
