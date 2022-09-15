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

namespace Flextype\Parsers\Expressions;

use Glowy\Arrays\Arrays;
use Glowy\Macroable\Macroable;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function Flextype\registry;
use function Flextype\entries;
use function Flextype\collection;

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
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.fetch.enabled')) {
            return collection();
        }

        // Backup current entry data
        $original = entries()->registry()->get('methods.fetch');

        // Do fetch the data from the resource.
        $result = entries()->fetch($id, $options);

        // Restore original entry data
        entries()->registry()->set('methods.fetch', $original);
        
        return $result;
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
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.registry.enabled')) {
            return collection();
        }

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
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.has.enabled')) {
            return false;
        }

        return entries()->has($id);
    }

    /**
     * Move entry.
     *
     * @param string $id    Unique identifier of the entry.
     * @param string $newID New Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function move(string $id, string $newID): bool
    {  
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.move.enabled')) {
            return false;
        }

        return entries()->move($id, $newID);
    }

    /**
     * Update entry.
     *
     * @param string $id   Unique identifier of the entry.
     * @param array  $data Data to update for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function update(string $id, array $data): bool
    {
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.update.enabled')) {
            return false;
        }

        return entries()->update($id, $data);
    }

    /**
     * Create entry.
     *
     * @param string $id   Unique identifier of the entry.
     * @param array  $data Data to create for the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function create(string $id, array $data = []): bool
    {
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.create.enabled')) {
            return false;
        }

        return entries()->create($id, $data);
    }

    /**
     * Delete entry.
     *
     * @param string $id Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function delete(string $id): bool
    {
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.delete.enabled')) {
            return false;
        }

        return entries()->delete($id);
    }

    /**
     * Copy entry.
     *
     * @param string $id    Unique identifier of the entry.
     * @param string $newID New Unique identifier of the entry.
     *
     * @return bool True on success, false on failure.
     *
     * @access public
     */
    public function copy(string $id, string $newID): bool
    {  
        if (! registry()->get('flextype.settings.parsers.expressions.expressions.entries.copy.enabled')) {
            return false;
        }

        return entries()->copy($id, $newID);
    }
}
