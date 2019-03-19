<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/admin/login', function (Request $request, Response $response, array $args) {
    return $this->view->render($response,
                               'plugins/admin/views/templates/users/login.html');
})->setName('login');

$app->get('/admin/profile', function (Request $request, Response $response, array $args) {
    return $this->view->render($response,
                               'plugins/admin/views/templates/users/profile.html', [
        'username' => Session::get('username'),
        'rolename' => Session::get('role'),
        'sidebar_menu_item' => 'profile'
    ]);
})->setName('profile');

$app->get('/admin/logout', function (Request $request, Response $response, array $args) {
    Session::destroy();
    return $response->withRedirect('/admin');
});

$app->get('/admin/registration', function (Request $request, Response $response, array $args) {
    return $this->view->render($response,
                               'plugins/admin/views/templates/users/registration.html');
})->setName('registration');

$app->post('/admin/registration', function (Request $request, Response $response, array $args) {
    if (UsersManager::processRegistrationForm($request->getParsedBody())) {
        return $response->withRedirect('admin');
    }
});

$app->post('/admin/login', function (Request $request, Response $response, array $args) {
    if (UsersManager::processLoginForm($request->getParsedBody())) {
        return $response->withRedirect('admin/entries');
    } else {
        Notification::set('error', __('admin_message_wrong_username_password'));
    }
});

class UsersManager
{
    public static function processLoginForm(array $data) : bool
    {
        if (Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $data['username'] . '.yaml')) {

            $user_file = YamlParser::decode(Filesystem::read($_user_file));

            if (password_verify(trim($data['password']), $user_file['hashed_password'])) {
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);

                return true;

            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function processRegistrationForm(array $data) : bool
    {
        if (!Filesystem::has($_user_file = PATH['site'] . '/accounts/' . Text::safeString($data['username']) . '.yaml')) {
            if (Filesystem::write(
                    PATH['site'] . '/accounts/' . $data['username'] . '.yaml',
                    YamlParser::encode(['username' => Text::safeString($data['username']),
                                        'hashed_password' => password_hash($data['password'], PASSWORD_BCRYPT),
                                        'email' => $data['email'],
                                        'role'  => 'admin',
                                        'state' => 'enabled']))) {
                                            return true;
                                        } else {
                                            return false;
                                        }
        } else {
            return false;
        }
    }

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
