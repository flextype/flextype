<?php

declare(strict_types=1);

namespace Flextype;

use FilesystemIterator;
use Flextype\Component\Number\Number;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function array_merge;
use function file_exists;
use function Flextype\Component\I18n\__;
use function getenv;
use function is_array;
use function php_sapi_name;
use function php_uname;
use function realpath;

/**
 * @property View $view
 * @property Router $router
 * @property Flash $flash
 */
class ToolsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        return $response->withRedirect($this->router->pathFor('admin.tools.information'));
    }

    /**
     * Information page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/information.html',
            [
                'menu_item' => 'tools',
                'php_uname' => php_uname(),
                'webserver' => $_SERVER['SERVER_SOFTWARE'] ?? @getenv('SERVER_SOFTWARE'),
                'php_sapi_name' => php_sapi_name(),
                'links' =>  [
                    'information' => [
                        'link' => $this->router->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                    'cache' => [
                        'link' => $this->router->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'registry' => [
                        'link' => $this->router->pathFor('admin.tools.registry'),
                        'title' => __('admin_registry'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                ],
            ]
        );
    }

    /**
     * Cache page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function cache(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/cache.html',
            [
                'menu_item' => 'tools',
                'doctrine_size' => Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/doctrine')),
                'glide_size' => Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/glide')),
                'twig_size' => Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/twig')),
                'links' =>  [
                    'information' => [
                        'link' => $this->router->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'cache' => [
                        'link' => $this->router->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                    'registry' => [
                        'link' => $this->router->pathFor('admin.tools.registry'),
                        'title' => __('admin_registry'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                ],
                'buttons' => [
                    'tools_clear_cache' => [
                        'type' => 'action',
                        'id' => 'clear-cache-all',
                        'link' => $this->router->pathFor('admin.tools.clearCacheAllProcess'),
                        'title' => __('admin_clear_cache_all'),
                        'attributes' => ['class' => 'float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Information page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function registry(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/registry.html',
            [
                'menu_item' => 'tools',
                'registry_dump' => $this->dotArray($this->registry->dump()),
                'links' =>  [
                    'information' => [
                        'link' => $this->router->pathFor('admin.tools.index'),
                        'title' => __('admin_information'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'cache' => [
                        'link' => $this->router->pathFor('admin.tools.cache'),
                        'title' => __('admin_cache'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'registry' => [
                        'link' => $this->router->pathFor('admin.tools.registry'),
                        'title' => __('admin_registry'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Clear cache process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function clearCacheProcess(Request $request, Response $response) : Response
    {
        $id = $request->getParsedBody()['cache-id'];

        $this->cache->clear($id);

        $this->flash->addMessage('success', __('admin_message_cache_files_deleted'));

        return $response->withRedirect($this->router->pathFor('admin.tools.cache'));
    }

    /**
     * Clear all cache process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function clearCacheAllProcess(Request $request, Response $response) : Response
    {
        $this->cache->clearAll();

        $this->flash->addMessage('success', __('admin_message_cache_files_deleted'));

        return $response->withRedirect($this->router->pathFor('admin.tools.cache'));
    }

    /**
     * dotArray
     */
    private function dotArray($array, $prepend = '') : array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, $this->dotArray($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * getDirectorySize
     */
    private function getDirectorySize($path)
    {
        $bytestotal = 0;
        $path       = realpath($path);
        if ($path!==false && $path!=='' && file_exists($path)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }

        return $bytestotal;
    }
}
