<?php

/**
 *
 * Flextype Admin Plugin
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\{Arr\Arr, Http\Http, Filesystem\Filesystem, Session\Session};
use Symfony\Component\Yaml\Yaml;



//
// Add listner for onPageBeforeRender event
//
if (Http::getUriSegment(0) == 'admin') {
    Events::addListener('onPageBeforeRender', function () {
        Admin::instance();
    });
}


class Admin {

    /**
     * An instance of the Admin class
     *
     * @var object
     * @access  protected
     */
    protected static $instance = null;

    /**
     * Is logged in
     *
     * @var bool
     * @access  protected
     */
    protected static $isLoggedIn = false;

    /**
     * Protected clone method to enforce singleton behavior.
     *
     * @access  protected
     */
    protected function __clone()
    {
        // Nothing here.
    }

    /**
     * Protected constructor since this is a static class.
     *
     * @access  protected
     */
    protected function __construct()
    {
        static::init();
        //Session::destroy();
    }

    protected static function init()
    {
        if (static::isLoggedIn()) {
            //static::getAdminPage();
            die('asd');
        } else {
            if (static::isUsersExists()) {
                die('1');
                static::getAuthPage();
            } else {
                die('2');
                static::getRegistrationPage();
            }
        }

        Http::requestShutdown();
    }

    protected static function getAdminPage()
    {
        die('asd');
        switch (Url::getUriSegment(1)) {
            case 'pages':
                static::getPagesManagerPage();
            break;
            case 'settings':
                static::getSettingsPage();
            break;
            default:
                static::getDashboardPage();
            break;
        }
    }

    protected static function getPagesManagerPage()
    {
        include 'views/pages.php';
    }

    protected static function getSettingsPage()
    {
        include 'views/settings.php';
    }

    protected static function getDashboardPage()
    {
        include 'views/dashboard.php';
    }

    protected static function getAuthPage()
    {

        $login = Http::post('login');

        if (isset($login)) {
            if (Filesystem::fileExists($_user_file = ACCOUNTS_PATH . '/' . Http::post('username') . '.yml')) {
                $user_file = Yaml::parseFile($_user_file);
                var_dump($user_file);
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);
            }
        }

        include 'views/login.php';
    }

    protected static function getRegistrationPage()
    {

        $registration = Http::post('registration');

        if (isset($registration)) {
            if (Filesystem::fileExists($_user_file = ACCOUNTS_PATH . '/' . Http::post('username') . '.yml')) {

            } else {
                $user = ['username' => Http::post('username'),
                         'password' => Http::post('password'),
                         'role'  => 'admin',
                         'state' => 'enabled'];

                Filesystem::setFileContent(ACCOUNTS_PATH . '/' . Http::post('username') . '.yml', Yaml::dump($user));

                Http::redirect('admin');
            }
        }

        include 'views/registration.php';
    }

    public static function isUsersExists()
    {
        $users = Filesystem::getFilesList(ACCOUNTS_PATH, 'yml');
        if (count($users) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isLoggedIn()
    {
        if (Session::exists('role') && Session::get('role') == 'admin') {
                    die('111');
            return true;
        } else {
            return false;
                    die('222');
        }
    }

    /**
     * Return the Admin instance.
     * Create it if it's not already created.
     *
     * @access public
     * @return object
     */
    public static function instance()
    {
        return !isset(self::$instance) and self::$instance = new Admin();
    }
}
