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

namespace Flextype\Entries\Expressions;

use Glowy\Arrays\Arrays;
use Glowy\Macroable\Macroable;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function Flextype\entries;

class EntriesExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [new ExpressionFunction('entries', static fn () => '(new EntriesExpressionsMethods())', static fn ($arguments) => (new EntriesExpressionsMethods()))];
    }
}

class EntriesExpressionsMethods
{
    use Macroable;

    /**
     * Fetch.
     *
     * @param string $id      Unique identifier of the entry.
     * @param array  $options Options array.
     *
     * @return \Glowy\Arrays\Arrays Returns instance of The Arrays class.
     *
     * @access public
     */
    public function fetch(string $id, array $options = []): \Glowy\Arrays\Arrays
    {
        return entries()->fetch($id, $options);
    }

    /**
     * Get Entries Registry.
     *
     * @return \Glowy\Arrays\Arrays Returns entries registry.
     *
     * @access public
     */
    public function registry(): \Glowy\Arrays\Arrays
    {
        return entries()->registry();
    }

    /**
     * Check whether entry exists
     *
     * @param string $id Unique identifier of the entry(entries).
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function has(string $id): bool
    {
        return entries()->has($id);
    }
}
