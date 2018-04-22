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
            static::getAdminPage();
        } else {
            if (static::isUsersExists()) {
                static::getAuthPage();
            } else {
                static::getRegistrationPage();
            }
        }

        Http::requestShutdown();
    }

    protected static function getAdminPage()
    {
        switch (Http::getUriSegment(1)) {
            case 'pages':
                static::getPagesManagerPage();
            break;
            case 'settings':
                static::getSettingsPage();
            break;
        }
    }

    protected static function getPagesManagerPage()
    {
        switch (Http::getUriSegment(2)) {
            case 'delete':
                if (Http::get('page') != '') {
                    Filesystem::deleteDir(PAGES_PATH . '/' . Http::get('page'));
                    Http::redirect('admin/views/pages/');
                }
            break;
            case 'add':


                $pages_list = Pages::getPages('', false , 'slug');

                $create_page = Http::post('create_page');

                if (isset($create_page)) {
                    if (Filesystem::setFileContent(PAGES_PATH . '/' . Http::post('parent_page') . '/' . Http::post('slug') . '/page.md',
                                              '---'."\n".
                                              'title: '.Http::post('title')."\n".
                                              '---'."\n")) {

                                        Http::redirect('admin/views/pages/');
                    }
                }

                View::factory('admin/views/pages/add')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
            case 'edit':

                $save_page = Http::post('save_page');

                if (isset($save_page)) {
                    Filesystem::setFileContent(PAGES_PATH . '/' . Http::post('slug') . '/page.md',
                                              '---'."\n".
                                              Http::post('frontmatter').
                                              '---'."\n".
                                              Http::post('editor'));
                }

                $page = trim(Filesystem::getFileContent(PAGES_PATH . '/' . Http::get('page') . '/page.md'));
                $page = explode('---', $page, 3);

                View::factory('admin/views/pages/editor')
                    ->assign('page_slug', Http::get('page'))
                    ->assign('page_frontmatter', $page[1])
                    ->assign('page_content', $page[2])
                    ->display();
            break;
            default:
                $pages_list = Pages::getPages('', false , 'title');

                View::factory('admin/views/pages/index')
                    ->assign('pages_list', $pages_list)
                    ->display();
            break;
        }
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
        return true;
        //echo Session::get('role');
        //if (Session::exists('role') && Session::get('role') == 'admin') {
        //    return true;
        //} else {
        //    return false;
        //}
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
