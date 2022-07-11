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

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

use function Flextype\fullUrlFor;
use function Flextype\getAbsoluteUrl;
use function Flextype\getBasePath;
use function Flextype\getBaseUrl;
use function Flextype\getCurrentUrl;
use function Flextype\getProjectUrl;
use function Flextype\getUriString;
use function Flextype\isCurrentUrl;
use function Flextype\redirect;
use function Flextype\urlFor;
use function Flextype\url;

class UrlExpression implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            new ExpressionFunction('url', static fn (string $string = '', string $prefix = 'base') => '\Flextype\url($string, $prefix)', static fn ($arguments, string $string = '', string $prefix = 'base') => urlFor($string, $prefix)),
            new ExpressionFunction('urlFor', static fn (string $routeName, array $data = [], array $queryParams = []) => '\Flextype\urlFor($routeName, $data, $queryParams)', static fn ($arguments, string $routeName, array $data = [], array $queryParams = []) => urlFor($routeName, $data, $queryParams)),
            new ExpressionFunction('fullUrlFor', static fn (ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => '\Flextype\fullUrlFor($request, $routeName, $data, $queryParams)', static fn ($arguments, ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []) => fullUrlFor($request, $routeName, $data = [], $queryParams = [])),
            new ExpressionFunction('isCurrentUrl', static fn (ServerRequestInterface $request, string $routeName, array $data = []) => '\Flextype\isCurrentUrl($request, $routeName, $data)', static fn ($arguments, ServerRequestInterface $request, string $routeName, array $data = []) => isCurrentUrl($request, $routeName, $data = [])),
            new ExpressionFunction('getCurrentUrl', static fn (ServerRequestInterface $request, bool $withQueryString = false) => '\Flextype\getCurrentUrl($request, $withQueryString)', static fn ($arguments, ServerRequestInterface $request, bool $withQueryString = false) => getCurrentUrl($request, $withQueryString)),
            new ExpressionFunction('getBasePath', static fn () => '\Flextype\getBasePath()', static fn () => getBasePath()),
            new ExpressionFunction('getBaseUrl', static fn () => '\Flextype\getBaseUrl()', static fn () => getBaseUrl()),
            new ExpressionFunction('getAbsoluteUrl', static fn () => '\Flextype\getAbsoluteUrl()', static fn () => getAbsoluteUrl()),
            new ExpressionFunction('getProjectUrl', static fn () => '\Flextype\getProjectUrl()', static fn () => getProjectUrl()),
            new ExpressionFunction('getUriString', static fn () => '\Flextype\getUriString()', static fn () => getUriString()),
            new ExpressionFunction('redirect', static fn (string $routeName, array $data = [], array $queryParams = [], int $status = 301) => '\Flextype\redirect($routeName, $data, $queryParams, $status)', static fn ($arguments, string $routeName, array $data = [], array $queryParams = [], int $status = 301) => redirect($routeName, $data, $queryParams, $status)),
        ];
    }
}
