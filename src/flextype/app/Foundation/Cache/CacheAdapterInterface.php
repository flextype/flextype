<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

interface CacheAdapterInterface
{
    /**
     * Injects the dependency container
     *
     * @return void
     */
    public function __construct($flextype);

    /**
     * Returns the cache driver object
     */
    public function getDriver() : object;
}
