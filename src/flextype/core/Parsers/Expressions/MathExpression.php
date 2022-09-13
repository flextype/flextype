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
use const PHP_ROUND_HALF_EVEN;
use const PHP_ROUND_HALF_UP;
use const PHP_ROUND_HALF_DOWN;
use const PHP_ROUND_HALF_ODD;

class MathExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('abs', static fn (int|float $num) => '\abs($num)', static fn (array $arguments, int|float $num): mixed => \abs($num)),
            new ExpressionFunction('round', static fn (int|float $num, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): mixed => '\round($num, $precision, $mode)', static fn (array $arguments, int|float $num, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): mixed => \round($num, $precision, $mode)),
            new ExpressionFunction('ceil', static fn (int|float $num) => '\ceil($num)', static fn (array $arguments, int|float $num): mixed => \ceil($num)),
            new ExpressionFunction('floor', static fn (int|float $num) => '\floor($num)', static fn (array $arguments, int|float $num): mixed => \floor($num)),
            new ExpressionFunction('min', static fn (mixed ...$values) => '\min($values)', static fn (array $arguments, mixed ...$values): mixed => \min($values)),
            new ExpressionFunction('max', static fn (mixed ...$values) => '\max($values)', static fn (array $arguments, mixed ...$values): mixed => \max($values)),
        ];
    }
}
