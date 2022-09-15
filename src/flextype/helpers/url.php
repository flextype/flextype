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

namespace Flextype;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

use function array_key_exists;
use function function_exists;
use function Glowy\Strings\strings;
use function str_replace;

if (! function_exists('urlFor')) {
    /**
     * Get the url for a named route.
     *
     * @param string                $routeName   Route name.
     * @param array<string, string> $data        Route placeholders.
     * @param array<string, string> $queryParams Query parameters.
     *
     * @return string Url for a named route.
     */
    function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return app()->getRouteCollector()->getRouteParser()->urlFor($routeName, $data, $queryParams);
    }
}

if (! function_exists('fullUrlFor')) {
    /**
     * Get the full url for a named route.
     *
     * @param ServerRequestInterface $request     Servert request interface.
     * @param string                 $routeName   Route name.
     * @param array<string, string>  $data        Route placeholders.
     * @param array<string, string>  $queryParams Query parameters.
     *
     * @return string Full url for a named route.
     */
    function fullUrlFor(ServerRequestInterface $request, string $routeName, array $data = [], array $queryParams = []): string
    {
        return app()->getRouteCollector()->getRouteParser()->fullUrlFor($request->getUri(), $routeName, $data, $queryParams);
    }
}

if (! function_exists('isCurrentUrl')) {
    /**
     * Determine is current url equal to route name.
     *
     * @param ServerRequestInterface $request   Servert request interface.
     * @param string                 $routeName Route name.
     * @param array<string, string>  $data      Route placeholders.
     */
    function isCurrentUrl(ServerRequestInterface $request, string $routeName, array $data = []): bool
    {
        $currentUrl = getBasePath() . $request->getUri()->getPath();
        $result     = app()->getRouteCollector()->getRouteParser()->urlFor($routeName, $data);

        return $result === $currentUrl;
    }
}

if (! function_exists('getCurrentUrl')) {
    /**
     * Get current path on given Uri.
     *
     * @param ServerRequestInterface $request         Servert request interface.
     * @param bool                   $withQueryString Get query string for current path.
     */
    function getCurrentUrl(ServerRequestInterface $request, bool $withQueryString = false): string
    {
        $currentUrl = getBasePath() . $request->getUri()->getPath();
        $query      = $request->getUri()->getQuery();

        if ($withQueryString && ! empty($query)) {
            $currentUrl .= '?' . $query;
        }

        return $currentUrl;
    }
}

if (! function_exists('getBasePath')) {
    /**
     * Get the base path.
     *
     * @return string Base Path.
     */
    function getBasePath(): string
    {
        return app()->getBasePath();
    }
}

if (! function_exists('setBasePath')) {
    /**
     * Set the base path.
     *
     * @param string $basePath Base path.
     */
    function setBasePath(string $basePath): void
    {
        app()->setBasePath($basePath);
    }
}

if (! function_exists('getBaseUrl')) {
    /**
     * Get the application base url.
     *
     * @return string Application base url.
     */
    function getBaseUrl(): string
    {
        $baseUrl  = registry()->get('flextype.settings.base_url') ?? '';
        $basePath = registry()->get('flextype.settings.base_path') ?? '';

        if ($baseUrl !== '') {
            return strings($baseUrl . '/' . $basePath)->reduceSlashes()->trimRight('/')->toString();
        }

        $getAuth = static function (): string {
            $result = '';
            if ($user = $_SERVER['PHP_AUTH_USER'] ?? '') {
                $result .= $user;

                if ($password = $_SERVER['PHP_AUTH_PW'] ?? '') {
                    $result .= ':' . $password;
                }

                $result .= '@';
            }

            return $result;
        };

        $isHttps = static function (): bool {
            if (array_key_exists('HTTPS', $_SERVER)) {
                return ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            }

            return false;
        };

        $url = '';

        $isHttps = $isHttps();

        $url .= $getAuth();

        $serverData = collection($_SERVER);

        $host = (string) $serverData->get('HTTP_HOST');
        $port = (int) $serverData->get('SERVER_PORT');
        $url .= str_replace(':' . $port, '', $host);

        if ($isHttps && $port !== 443) {
            $url .= $port ? ":{$port}" : '';
        } elseif (! $isHttps && $port !== 80) {
            $url .= $port ? ":{$port}" : '';
        }

        if ($url) {
            if ($isHttps) {
                $url = 'https://' . $url;
            } else {
                $url = 'http://' . $url;
            }
        }

        return strings($url . '/' . $basePath)->reduceSlashes()->trimRight('/')->toString();
    }
}

if (! function_exists('getAbsoluteUrl')) {
    /**
     * Get the application absolute url.
     *
     * @return string Application absolute url.
     */
    function getAbsoluteUrl(): string
    {
        $url  = getBaseUrl();
        $url .= '/';
        $url .= $_SERVER['REQUEST_URI'] ?? '';

        return strings($url)->reduceSlashes()->trimRight('/')->toString();
    }
}

if (! function_exists('getProjectUrl')) {
    /**
     * Get the application project url.
     *
     * @return string Application project url.
     */
    function getProjectUrl(): string
    {
        $url  = getBaseUrl();
        $url .= '/';
        $url .= FLEXTYPE_PROJECT_NAME;

        return strings($url)->reduceSlashes()->trimRight('/')->toString();
    }
}

if (! function_exists('getUriString')) {
    /**
     * Get uri string.
     */
    function getUriString(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }
}

if (! function_exists('url')) {
    /**
     * Build URl Strings.
     *
     * @return string URl Strings.
     */
    function url(string $string = '', string $prefix = 'base'): string
    {
        switch ($prefix) {
            case 'project':
                $url = getProjectUrl();
                break;
            
            case 'base':
            default:
                $url = getBaseUrl();
                break;
        }

        $url .= '/';
        $url .= $string;

        return strings($url)->reduceSlashes()->trimRight('/')->toString();
    }
}


if (! function_exists('redirect')) {
    /**
     * Redirect.
     *
     * @param string                $routeName   Route name.
     * @param array<string, string> $data        Route placeholders.
     * @param array<string, string> $queryParams Query parameters.
     * @param int                   $status      Status code.
     */
    function redirect(string $routeName, array $data = [], array $queryParams = [], int $status = 301): Response
    {
        $response = new Response();
        $response = $response->withStatus($status);
        $response = $response->withHeader('Location', urlFor($routeName, $data, $queryParams));

        return $response;
    }
}
