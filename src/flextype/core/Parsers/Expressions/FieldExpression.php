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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use Glowy\Macroable\Macroable;
use function Flextype\entries;
use function Glowy\Strings\strings;

class FieldExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('field', 
                static function (string $field) {
                    $field = strings($field)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->get('methods.fetch.result.$field');";
                    return $code;
                },
                static fn ($arguments, string $field) => entries()->registry()->get('methods.fetch.result.' . $field)
            ),
            new ExpressionFunction('fields', 
                                   static fn () => "(new \\Flextype\\Parsers\\Expressions\\FieldsExpressionMethods())", 
                                   static fn ($arguments) => (new FieldsExpressionMethods())
            )
        ];
    }
}

class FieldsExpressionMethods
{
    use Macroable;

    public function set(string|null $key, $value)
    {
        entries()->registry()->set('methods.fetch.result.' . $key, $value);
    }

    public function get($key, $default = null)
    {
        return entries()->registry()->get('methods.fetch.result.' . $key, $default);
    }

    public function unset(string|null $key)
    {
        entries()->registry()->set('methods.fetch.result.' . $key, null);
    }

    public function delete($key)
    {
        return entries()->registry()->delete('methods.fetch.result.' . $key);
    }
}