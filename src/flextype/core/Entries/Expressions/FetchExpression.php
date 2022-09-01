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

use Glowy\Macroable\Macroable;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use function Flextype\fetch;
use function Flextype\entries;

class FetchExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [new ExpressionFunction('fetch', static fn (string $resource, array $options = []) => '(new \Flextype\Entries\Expressions\FetchExpressionsMethods())->fetch($resource, $options);', static fn ($arguments, string $resource, array $options = []) => (new \Flextype\Entries\Expressions\FetchExpressionsMethods())->fetch($resource, $options))];
    }
}

class FetchExpressionsMethods
{
    use Macroable;

    /**
     * Fetch data from entries, local files and urls.
     *
     * @param string $resource A resource that you wish to fetch.
     * @param array $options Options.
     * @return mixed Returns the data from the resource or empty collection on failure.
     */
    function fetch(string $resource, array $options = [])
    {
        // Backup current entry data
        $original = entries()->registry()->get('methods.fetch');

        // Do fetch the data from the resource.
        $result = fetch($resource, $options);

        // Restore original entry data
        entries()->registry()->set('methods.fetch', $original);
                
        return $result;
    }
}