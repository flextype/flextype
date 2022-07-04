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

declare(strict_types=1);

namespace Phpfastcache\Drivers\Phparray;

use Phpfastcache\Config\IOConfigurationOptionInterface;
use Phpfastcache\Config\IOConfigurationOption;

class Config extends IOConfigurationOption implements IOConfigurationOptionInterface
{

    /**
     * @param string $cacheFileExtension
     * @return self
     * @throws PhpfastcacheInvalidConfigurationException
     */
    public function setCacheFileExtension(string $cacheFileExtension): static
    {
        $this->cacheFileExtension = 'php';
        return $this;
    }
}