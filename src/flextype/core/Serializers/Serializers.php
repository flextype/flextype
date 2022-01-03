<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
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
