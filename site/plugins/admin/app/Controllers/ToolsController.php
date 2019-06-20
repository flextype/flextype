<?php

namespace Flextype;

use function Flextype\Component\I18n\__;
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

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/tools/cache.html',
            [
                'menu_item' => 'tools',
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
                        ]
            ]
        );
    }
}
