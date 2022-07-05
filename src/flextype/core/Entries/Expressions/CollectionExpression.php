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
use function Flextype\collection;
use function Flextype\collectionFromJson;
use function Flextype\collectionFromString;
use function Flextype\collectionWithRange;
use function Flextype\collectionFromQueryString;
use function Flextype\filterCollection;

class CollectionExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('collection', fn($items = null) => '\Flextype\collection($items)', fn($arguments, $items = null) => collection($items)),
            new ExpressionFunction('collectionFromJson', fn(string $input, bool $assoc = true, int $depth = 512, int $flags = 0) => '\Flextype\ccollectionFromJson($input, $assoc, $depth, $flags)', fn($arguments, string $input, bool $assoc = true, int $depth = 512, int $flags = 0) => collectionFromJson($input, $assoc, $depth, $flags)),
            new ExpressionFunction('collectionFromString', fn(string $string, string $separator) => '\Flextype\ccollectionFromString($string, $separator)', fn($arguments, string $string, string $separator) => collectionFromString($string, $separator)),
            new ExpressionFunction('collectionWithRange', fn($low, $high, int $step = 1) => '\Flextype\ccollectionWithRange($low, $high, $step)', fn($arguments, $low, $high, int $step = 1) => collectionWithRange($low, $high, $step)),
            new ExpressionFunction('collectionFromQueryString', fn(string $string) => '\Flextype\ccollectionFromQueryString($string)', fn($arguments, string $string) => collectionFromQueryString($string)),
            new ExpressionFunction('filterCollection', fn($items = [], array $options = []) => '\Flextype\cfilterCollection($items, $options)', fn($arguments, $items = [], array $options = []) => filterCollection($items, $options)),
        ];
    }
}