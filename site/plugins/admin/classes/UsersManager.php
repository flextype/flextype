<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;

$app->get('/admin/login', UsersController::class . ':login')->setName('admin.login');
$app->get('/admin/profile', UsersController::class . ':profile')->setName('admin.profile');
$app->post('/admin/login', UsersController::class . ':processLoginForm');
$app->get('/admin/logout', UsersController::class . ':processLogoutForm')->setName('admin.logout');
$app->get('/admin/registration', UsersController::class . ':registration')->setName('admin.registration');
$app->post('/admin/registration', UsersController::class . ':processRegistrationForm');

class UsersController {

    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function login($request, $response, $args)
    {
        return $this->container->get('view')->render($response,
                                   'plugins/admin/views/templates/users/login.html');
    }

    public function profile($request, $response, $args)
    {
        return $this->container->get('view')->render($response,
                                   'plugins/admin/views/templates/users/profile.html', [
            'username' => Session::get('username'),
            'rolename' => Session::get('role'),
            'sidebar_menu_item' => 'profile'
        ]);
    }

    public function processLoginForm($request, $response, $args)
    {
        if (Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $data['username'] . '.yaml')) {

            $user_file = YamlParser::decode(Filesystem::read($_user_file));

            if (password_verify(trim($data['password']), $user_file['hashed_password'])) {
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);

                return $response->withRedirect('admin/entries');

            } else {
                //Notification::set('error', __('admin_message_wrong_username_password'));
            }
        } else {
            //Notification::set('error', __('admin_message_wrong_username_password'));
        }
    }

    public function processLogoutForm($request, $response, $args)
    {
        Session::destroy();
        return $response->withRedirect('/admin');
    }

    public function registration($request, $response, $args)
    {
        return $this->view->render($response,
                                   'plugins/admin/views/templates/users/registration.html');
    }

    public function processRegistrationForm($request, $response, $args)
    {
        if (!Filesystem::has($_user_file = PATH['site'] . '/accounts/' . Text::safeString($data['username']) . '.yaml')) {
            if (Filesystem::write(
                    PATH['site'] . '/accounts/' . $data['username'] . '.yaml',
                    YamlParser::encode(['username' => Text::safeString($data['username']),
                                        'hashed_password' => password_hash($data['password'], PASSWORD_BCRYPT),
                                        'email' => $data['email'],
                                        'role'  => 'admin',
                                        'state' => 'enabled']))) {
                                            return $response->withRedirect('admin/entries');
                                        } else {
                                            //return false;
                                        }
        } else {
            //return false;
        }
    }
}

class Users
{
    public static function isUsersExists() : bool
    {
        // Get Users Profiles
        $users = Filesystem::listContents(PATH['site'] . '/accounts/');

        // If any users exists then return true
        return ($users && count($users) > 0) ? true : false;
    }

    public static function isLoggedIn()
    {
        return (Session::exists('role') && Session::get('role') == 'admin') ? true : false;
    }
}
