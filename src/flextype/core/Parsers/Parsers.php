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

namespace Flextype\Parsers;

use Glowy\Macroable\Macroable;

class Parsers
{
    use Macroable;

    /**
     * Get a Shortcodes instance.
     */
    public function shortcodes(): Shortcodes
    {
        return Shortcodes::getInstance();
    }

    /**
     * Get a Markdown instance.
     */
    public function markdown(): Markdown
    {
        return Markdown::getInstance();
    }

    /**
     * Get a Textile instance.
     */
    public function textile(): Textile
    {
        return Textile::getInstance();
    }

    /**
     * Get a Expressions instance.
     */
    public function expressions(): Expressions
    {
        return Expressions::getInstance();
    }
}
