<?php

namespace Flextype;

use function Flextype\Component\I18n\__;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Number\Number;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property View $view
 * @property Router $router
 */
class ToolsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
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
     *
     * @return Response
     */
    public function information(Request $request, Response $response) : Response
    {

        if (function_exists('apache_get_modules')) {
            if (!in_array('mod_rewrite', apache_get_modules())) {
                $apache_mod_rewrite_installed = false;
            } else {
                $apache_mod_rewrite_installed = true;
            }
        } else {
            $apache_mod_rewrite_installed = true;
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/information.html',
            [
                'menu_item' => 'tools',
                'php_uname' => php_uname(),
                'webserver' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'),
                'php_sapi_name' => php_sapi_name(),
                'apache_mod_rewrite_installed' => $apache_mod_rewrite_installed,
                'links' =>  [
                                'information' => [
                                    'link' => $this->router->pathFor('admin.tools.index'),
                                    'title' => __('admin_information'),
                                    'attributes' => ['class' => 'navbar-item active']
                                ],
                                'cache' => [
                                    'link' => $this->router->pathFor('admin.tools.cache'),
                                    'title' => __('admin_cache'),
                                    'attributes' => ['class' => 'navbar-item']
                                ],
                        ]
            ]
        );
    }

    /**
     * Cache page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function cache(Request $request, Response $response) : Response
    {

        $doctrine_size = Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/doctrine'));
        $glide_size = Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/glide'));
        $twig_size = Number::byteFormat($this->getDirectorySize(PATH['cache'] . '/twig'));

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/cache.html',
            [
                'menu_item' => 'tools',
                'doctrine_size' => $doctrine_size,
                'glide_size' => $glide_size,
                'twig_size' => $twig_size,
                'links' =>  [
                                'information' => [
                                    'link' => $this->router->pathFor('admin.tools.index'),
                                    'title' => __('admin_information'),
                                    'attributes' => ['class' => 'navbar-item']
                                ],
                                'cache' => [
                                    'link' => $this->router->pathFor('admin.tools.cache'),
                                    'title' => __('admin_cache'),
                                    'attributes' => ['class' => 'navbar-item active']
                                ],
                        ],
                'buttons' => [
                    'settings_clear_cache' => [
                        'type' => 'action',
                        'id' => 'clear-cache',
                        'link' => $this->router->pathFor('admin.settings.clear-cache'),
                        'title' => __('admin_clear_cache_all'),
                        'attributes' => ['class' => 'float-right btn']
                    ]
                ]
            ]
        );
    }


    public function clearCacheProcess($request, $response)
    {
        $id = $request->getParsedBody()['cache-id'];

        $this->cache->clear($id);

        $this->flash->addMessage('success', __('admin_message_cache_files_deleted'));

        return $response->withRedirect($this->router->pathFor('admin.tools.cache'));
    }


    public function getDirectorySize($path)
    {
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
}
