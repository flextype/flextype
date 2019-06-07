<?php

namespace Flextype;

use function Flextype\Component\I18n\__;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property View  $view
 * @property Router $router
 */
class InformationController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
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
            'plugins/admin/views/templates/system/information/index.html',
            [
            'menu_item' => 'information',
            'php_uname' => php_uname(),
            'webserver' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'),
            'php_sapi_name' => php_sapi_name(),
            'apache_mod_rewrite_installed' => $apache_mod_rewrite_installed,
            'links' =>  [
                            'information' => [
                            'link' => $this->router->pathFor('admin.information.index'),
                            'title' => __('admin_information'),
                            'attributes' => ['class' => 'navbar-item active']
                        ],
            ]
        ]
        );
    }
}
