<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Phpfastcache\Drivers\Phparray;

use FilesystemIterator;
use Phpfastcache\Cluster\AggregatablePoolInterface;
use Phpfastcache\Core\Item\ExtendedCacheItemInterface;
use Phpfastcache\Core\Pool\IO\IOHelperTrait;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheIOException;
use Phpfastcache\Exceptions\PhpfastcacheLogicException;
use Phpfastcache\Util\Directory;
use Throwable;

use function clearstatcache;
use function dirname;
use function file_exists;
use function Flextype\serializers;
use function is_dir;
use function is_writable;
use function mkdir;
use function rmdir;
use function unlink;

/**
 * @method Config getConfig()
 *
 * Important NOTE:
 * We are using getKey instead of getEncodedKey since this backend create filename that are
 * managed by defaultFileNameHashFunction and not defaultKeyHashFunction
 */
class Driver implements AggregatablePoolInterface
{
    use IOHelperTrait;

    private static string $ext = 'php';

    /**
     * @throws PhpfastcacheIOException
     * @throws PhpfastcacheInvalidArgumentException
     */
    public function driverCheck(): bool
    {
        return is_writable($this->getPath()) || mkdir($concurrentDirectory = $this->getPath(), $this->getDefaultChmod(), true) || is_dir($concurrentDirectory);
    }

    protected function driverConnect(): bool
    {
        return true;
    }

    /**
     * @return ?array<string, mixed>
     *
     * @throws PhpfastcacheIOException
     */
    protected function driverRead(ExtendedCacheItemInterface $item): ?array
    {
        $filePath = $this->getFilePath($item->getKey(), true) . '.' . 'php';

        $value = null;

        set_error_handler(static function () {});

        $value = include $filePath;
        
        restore_error_handler();

        return ! is_bool($value) ? $value : null;
    }

    /**
     * @throws PhpfastcacheIOException
     * @throws PhpfastcacheInvalidArgumentException
     * @throws PhpfastcacheLogicException
     */
    protected function driverWrite(ExtendedCacheItemInterface $item): bool
    {
        $this->assertCacheItemType($item, Item::class);

        $filePath = $this->getFilePath($item->getKey()) . '.' . 'php';
        $data     = $this->driverPreWrap($item);

        try {
            return $this->writeFile($filePath, serializers()->phparray()->encode($data), $this->getConfig()->isSecureFileManipulation());
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws PhpfastcacheIOException
     * @throws PhpfastcacheInvalidArgumentException
     */
    protected function driverDelete(ExtendedCacheItemInterface $item): bool
    {
        $this->assertCacheItemType($item, Item::class);

        $filePath = $this->getFilePath($item->getKey(), true) . '.' . 'php';;
        if (file_exists($filePath) && @unlink($filePath)) {
            clearstatcache(true, $filePath);
            $dir = dirname($filePath);
            if (! (new FilesystemIterator($dir))->valid()) {
                rmdir($dir);
            }

            return true;
        }

        return false;
    }

    /**
     * @throws PhpfastcacheIOException
     * @throws PhpfastcacheInvalidArgumentException
     */
    protected function driverClear(): bool
    {
        return Directory::rrmdir($this->getPath(true));
    }
}
