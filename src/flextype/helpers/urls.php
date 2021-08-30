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
     * Get the base path
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
     * Set the base path
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

if (! function_exists('redirect')) {
    /**
     * Redirect
     *
     * @param string                $routeName   Route name
     * @param array<string, string> $data        Route placeholders
     * @param array<string, string> $queryParams Query parameters
     *
     * @return Response 
     */
    function redirect(string $routeName, array $data = [], array $queryParams = []): Response
    {
        $response = new Response();
        $response->withHeader('Location', urlFor($routeName, $data, $queryParams));

        return $response;
    }
}