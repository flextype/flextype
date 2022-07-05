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
use function Flextype\urlFor;
use function Flextype\fullUrlFor;
use function Flextype\isCurrentUrl;
use function Flextype\getCurrentUrl;
use function Flextype\getBasePath;
use function Flextype\getBaseUrl;
use function Flextype\getAbsoluteUrl;
use function Flextype\getProjectUrl;
use function Flextype\getUriString;
use function Flextype\redirect;

class UrlExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('urlFor', fn(string $routeName, array $data = [], array $queryParams = []) => '\Flextype\urlFor($routeName, $data, $queryParams)', fn(string $routeName, array $data = [], array $queryParams = []) => urlFor($routeName, $data, $queryParams)),
            new ExpressionFunction('fullUrlFor', fn(\Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => '\Flextype\fullUrlFor($request, $routeName, $data, $queryParams)', fn(\Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => fullUrlFor($request, $routeName, $data = [], $queryParams = [])),
            new ExpressionFunction('isCurrentUrl', fn(\Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = []) => '\Flextype\isCurrentUrl($request, $routeName, $data)', fn(\Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = []) => isCurrentUrl($request, $routeName, $data = [])),
            new ExpressionFunction('getCurrentUrl', fn(\Psr\Http\Message\ServerRequestInterface $request, bool $withQueryString = false) => '\Flextype\getCurrentUrl($request, $withQueryString)', fn(\Psr\Http\Message\ServerRequestInterface $request, bool $withQueryString = false) => getCurrentUrl($request, $withQueryString)),
            new ExpressionFunction('getBasePath', fn() => '\Flextype\getBasePath()', fn() => getBasePath()),
            new ExpressionFunction('getBaseUrl', fn() => '\Flextype\getBaseUrl()', fn() => getBaseUrl()),
            new ExpressionFunction('getAbsoluteUrl', fn() => '\Flextype\getAbsoluteUrl()', fn() => getAbsoluteUrl()),
            new ExpressionFunction('getProjectUrl', fn() => '\Flextype\getProjectUrl()', fn() => getProjectUrl()),
            new ExpressionFunction('getUriString', fn() => '\Flextype\getUriString()', fn() => getUriString()),
            new ExpressionFunction('redirect', fn(string $routeName, array $data = [], array $queryParams = [], int $status = 301) => '\Flextype\redirect($routeName, $data, $queryParams, $status)', fn(string $routeName, array $data = [], array $queryParams = [], int $status = 301) => redirect($routeName, $data, $queryParams, $status))
        ];
    }
}