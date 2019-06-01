<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;

class InformationController extends Controller
{
    public function index($request, $response, $args)
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

        if (!function_exists('password_hash')) {
            $password_hash_installed = false;
        } else {
            $password_hash_installed = true;
        }

        if (!function_exists('password_verify')) {
            $password_verify_installed = false;
        } else {
            $password_verify_installed = true;
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
           'password_verify_installed' => $password_verify_installed,
           'password_hash_installed' => $password_hash_installed,
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
