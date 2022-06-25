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

class UrlExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('urlFor', fn(string $routeName, array $data = [], array $queryParams = []) => 'urlFor($routeName, $data, $queryParams)', fn(string $routeName, array $data = [], array $queryParams = []) => urlFor($routeName, $data, $queryParams)),
            new ExpressionFunction('fullUrlFor', fn(Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => 'fullUrlFor($request, $routeName, $data, $queryParams)', fn(Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => fullUrlFor($request, $routeName, $data = [], $queryParams = [])),
            new ExpressionFunction('isCurrentUrl', fn(Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = []) => 'isCurrentUrl($request, $routeName, $data)', fn(Psr\Http\Message\ServerRequestInterface $request, string $routeName, array $data = []) => isCurrentUrl($request, $routeName, $data = [])),
            new ExpressionFunction('getCurrentUrl', fn(Psr\Http\Message\ServerRequestInterface $request, bool $withQueryString = false) => 'getCurrentUrl($request, $withQueryString)', fn(Psr\Http\Message\ServerRequestInterface $request, bool $withQueryString = false) => getCurrentUrl($request, $withQueryString)),
            new ExpressionFunction('getBasePath', fn() => 'getBasePath()', fn() => getBasePath()),
            new ExpressionFunction('getBaseUrl', fn() => 'getBaseUrl()', fn() => getBaseUrl()),
            new ExpressionFunction('setBasePath', fn(string $basePath) => 'setBasePath($basePath)', fn(string $basePath) => setBasePath($basePath)),
            new ExpressionFunction('getBaseUrl', fn() => 'getBaseUrl()', fn() => getBaseUrl()),
            new ExpressionFunction('getAbsoluteUrl', fn() => 'getAbsoluteUrl()', fn() => getAbsoluteUrl()),
            new ExpressionFunction('getProjectUrl', fn() => 'getProjectUrl()', fn() => getProjectUrl()),
            new ExpressionFunction('getUriString', fn() => 'getUriString()', fn() => getUriString()),
            new ExpressionFunction('redirect', fn(string $routeName, array $data = [], array $queryParams = [], int $status = 301) => 'redirect($routeName, $data, $queryParams, $status)', fn(string $routeName, array $data = [], array $queryParams = [], int $status = 301) => redirect($routeName, $data, $queryParams, $status))
        ];
    }
}