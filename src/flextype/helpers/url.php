<?php 

declare(strict_types=1);

use Slim\Interfaces\RouteParserInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response;

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
     * @param ServerRequestInterface $request     Servert request interface.
     * @param string                 $routeName   Route name.
     * @param array<string, string>  $data        Route placeholders.
     *
     * @return bool
     */
    function isCurrentUrl(ServerRequestInterface $request, string $routeName, array $data = []): bool
    {
        $currentUrl = getBasePath() . $request->getUri()->getPath();
        $result = app()->getRouteCollector()->getRouteParser()->urlFor($routeName, $data);

        return $result === $currentUrl;
    }
}

if (! function_exists('getCurrentUrl')) {
    /**
     * Get current path on given Uri.
     *
     * @param ServerRequestInterface $request         Servert request interface.
     * @param bool                   $withQueryString Get query string for current path.
     *
     * @return string
     */
    function getCurrentUrl(ServerRequestInterface $request, bool $withQueryString = false): string
    {
        $currentUrl = getBasePath() . $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();

        if ($withQueryString && !empty($query)) {
            $currentUrl .= '?'.$query;
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
     *
     * @return void
     */
    function setBasePath(string $basePath)
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

        if ($baseUrl != '') {
            return $baseUrl . $basePath;
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
                return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
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
        } elseif (!$isHttps && $port !== 80) {
            $url .= $port ? ":{$port}" : '';
        }

        if ($url) {
            if ($isHttps) {
                $url = 'https://' . $url . '/';
            } else {
                $url = 'http://' . $url . '/';
            }
        }

        $url .= $basePath;

        return $url;
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
        $url .= $_SERVER['REQUEST_URI'] ?? '';

        return $url;
    }
}

if (! function_exists('getUriString')) {
    /**
     * Get uri string.
     *
     * @return string
     */
    function getUriString(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '';
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
     *
     * @return Response 
     */
    function redirect(string $routeName, array $data = [], array $queryParams = [], int $status = 301): Response
    {
        $response = new Response();
        $response = $response->withStatus($status);
        $response = $response->withHeader('Location', urlFor($routeName, $data, $queryParams));

        return $response;
    }
}