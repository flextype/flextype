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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function Flextype\entries;
use function Glowy\Strings\strings;

class VarExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('var', 
                static function (string $var) {
                    $var = strings($var)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->get('methods.fetch.result.$var');";
                    return $code;
                },
                static function ($arguments, string $var) {
                    return entries()->registry()->get('methods.fetch.result.vars.' . $var);
                }
            ),
            new ExpressionFunction('set', 
                static function (string $var, $value) {
                    $var = strings($var)->stripQuotes()->toString();
                    $value = strings($value)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->set('methods.fetch.result.$var', '$value');";
                    return $code;
                }, 
                static function ($arguments, string $var, $value) {
                    entries()->registry()->set('methods.fetch.result.' . $var, $value);
                }
            ),
            new ExpressionFunction('get', 
                static function (string $var, $default = '') {
                    $var = strings($var)->stripQuotes()->toString();
                    $default = strings($default)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->get('methods.fetch.result.$var'" . ($default ? ", '$default'" : '') . ");";
                    return $code;
                },
                static function ($arguments, string $var, $default = '') {
                    return entries()->registry()->get('methods.fetch.result.' . $var, $default);
                }
            ),
            new ExpressionFunction('delete', 
                static function (string $var) {
                    $var = strings($var)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->delete('methods.fetch.result.$var');";
                    return $code;
                },
                static function ($arguments, string $var) {
                    entries()->registry()->delete('methods.fetch.result.' . $var);
                }
            ),
            new ExpressionFunction('unset', 
                static function (string $var) {
                    $var = strings($var)->stripQuotes()->toString();
                    $code = "\Flextype\\entries()->registry()->set('methods.fetch.result.$var', null);";
                    return $code;
                }, 
                static function ($arguments, string $var) {
                    entries()->registry()->set('methods.fetch.result.' . $var, null);
                }
            ),
        ];
    }
}
