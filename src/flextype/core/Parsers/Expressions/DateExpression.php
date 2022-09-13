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

use function Flextype\registry;

class DateExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('date', static fn (string $format, ?int $timestamp = null) => '\date($format, $timestamp)', static fn (array $arguments, string $format, ?int $timestamp = null): string => \date($format, $timestamp)),
            new ExpressionFunction('time', static fn () => '\time()', static fn (array $arguments): int => \time()),
            new ExpressionFunction('strtotime', static fn (string $datetime, ?int $baseTimestamp = null) => '\strtotime($datetime, $baseTimestamp)', static fn (array $arguments, string $datetime, ?int $baseTimestamp = null): int|false => \strtotime($datetime, $baseTimestamp))
        ];
    }
}
