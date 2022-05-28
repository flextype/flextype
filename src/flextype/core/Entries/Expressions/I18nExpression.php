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

class I18nExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('tr', fn(string $translate, array $values = [], string $locale = null) => '__($translate, $values, $locale)', fn($arguments, string $translate, array $values = [], string $locale = null) => __($translate, $values, $locale)),
            new ExpressionFunction('__', fn(string $translate, array $values = [], string $locale = null) => '__($translate, $values, $locale)', fn($arguments, string $translate, array $values = [], string $locale = null) => __($translate, $values, $locale)),
        ];
    }
}