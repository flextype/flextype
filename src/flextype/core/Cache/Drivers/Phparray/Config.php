<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Phpfastcache\Drivers\Phparray;

use Phpfastcache\Config\ConfigurationOption;

class Config extends ConfigurationOption
{
    /**
     * @var boolean
     */
    protected $secureFileManipulation = false;

    /**
     * @var bool
     */
    protected $htaccess = true;

    /**
     * @var string
     */
    protected $securityKey = '';

    /**
     * @var string
     */
    protected $cacheFileExtension = 'txt';

    /**
     * @return string
     */
    public function getSecurityKey(): string
    {
        return $this->securityKey;
    }

    /**
     * @param string $securityKey
     * @return Config
     */
    public function setSecurityKey(string $securityKey): self
    {
        $this->securityKey = $securityKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHtaccess(): bool
    {
        return $this->htaccess;
    }

    /**
     * @param bool $htaccess
     * @return Config
     */
    public function setHtaccess(bool $htaccess): self
    {
        $this->htaccess = $htaccess;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSecureFileManipulation(): bool
    {
        return $this->secureFileManipulation;
    }

    /**
     * @param bool $secureFileManipulation
     * @return self
     */
    public function setSecureFileManipulation(bool $secureFileManipulation): self
    {
        $this->secureFileManipulation = $secureFileManipulation;
        return $this;
    }


    /**
     * @return string
     */
    public function getCacheFileExtension(): string
    {
        return $this->cacheFileExtension;
    }

    /**
     * @param string $cacheFileExtension
     * @return self
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public function setCacheFileExtension(string $cacheFileExtension): self
    {
        $this->cacheFileExtension = 'php';
        return $this;
    }
}