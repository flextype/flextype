<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;


use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/admin/information', function (Request $request, Response $response, array $args) {

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

    return $this->view->render($response,
                               'plugins/admin/views/templates/system/information/index.html', [
        'menu_item' => 'information',
        'php_uname' => php_uname(),
        'webserver' => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'),
        'php_sapi_name' => php_sapi_name(),
        'apache_mod_rewrite_installed' => $apache_mod_rewrite_installed,
        'password_verify_installed' => $password_verify_installed,
        'password_hash_installed' => $password_hash_installed

    ]);
})->setName('information');

class InformationManager
{
    public static function getInformationManager()
    {
        Registry::set('sidebar_menu_item', 'infomation');

        Themes::view('admin/views/templates/system/information/list')->display();
    }

    /**
     * Tests whether a file is writable for anyone.
     *
     * @param  string  $file File to check
     * @return bool
     */
    public static function isFileWritable(string $file) : bool
    {
        // Is file exists ?
        if (! file_exists($file)) {
            throw new RuntimeException(vsprintf("%s(): The file '{$file}' doesn't exist", array(__METHOD__)));
        }

        // Gets file permissions
        $perms = fileperms($file);

        // Is writable ?
        if (is_writable($file) || ($perms & 0x0080) || ($perms & 0x0010) || ($perms & 0x0002)) {
            return true;
        }
    }
}
