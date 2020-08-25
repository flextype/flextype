<?php

declare(strict_types=1);

namespace Flextype\App\Foundation\Cache;

use Doctrine\Common\Cache\RedisCache;
use Redis;
use RedisException;

class RedisCacheAdapter implements CacheAdapterInterface
{
    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {

    }

    public function getDriver() : object
    {
        $redis    = new Redis();
        $socket   = flextype('registry')->get('flextype.settings.cache.redis.socket', false);
        $password = flextype('registry')->get('flextype.settings.cache.redis.password', false);

        if ($socket) {
            $redis->connect($socket);
        } else {
            $redis->connect(
                flextype('registry')->get('flextype.settings.cache.redis.server', 'localhost'),
                flextype('registry')->get('flextype.settings.cache.redis.port', 6379)
            );
        }

        // Authenticate with password if set
        if ($password && ! $redis->auth($password)) {
            throw new RedisException('Redis authentication failed');
        }

        $driver = new RedisCache();
        $driver->setRedis($redis);

        return $driver;
    }
}
