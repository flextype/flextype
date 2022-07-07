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

namespace Flextype\Serializers;

use Glowy\Macroable\Macroable;

class Serializers
{
    use Macroable;

    /**
     * Create a Json instance.
     */
    public function json(): Json
    {
        return new Json();
    }

    /**
     * Create a Json instance.
     */
    public function json5(): Json5
    {
        return new Json5();
    }

    /**
     * Create a Yaml instance.
     */
    public function yaml(): Yaml
    {
        return new Yaml();
    }

    /**
     * Create a Frontmatter instance.
     */
    public function frontmatter(): Frontmatter
    {
        return new Frontmatter();
    }

    /**
     * Create a Neon instance.
     */
    public function neon(): Neon
    {
        return new Neon();
    }

    /**
     * Create a PhpArray instance.
     */
    public function phparray(): PhpArray
    {
        return new PhpArray();
    }
}
