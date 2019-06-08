<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;

class UsersController extends Controller
{
    public function profile($request, $response)
    {
        return $this->container->get('view')->render(
            $response,
            'plugins/admin/views/templates/users/profile.html'
        );
    }

    public function login($request, $response)
    {
        // Get Users Profiles
        $users = Filesystem::listContents(PATH['site'] . '/accounts/');

        if ($users && count($users) > 0) {
            return $this->container->get('view')->render(
                $response,
                'plugins/admin/views/templates/users/login.html'
            );
        } else {
            return $response->withRedirect($this->router->pathFor('admin.users.registration'));
        }

    }

    public function loginProcess($request, $response)
    {
        $data = $request->getParsedBody();

        if (Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $data['username'] . '.json')) {
            $user_file = JsonParser::decode(Filesystem::read($_user_file));
            if (password_verify(trim($data['password']), $user_file['hashed_password'])) {
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);
                return $response->withRedirect($this->router->pathFor('admin.entries.index'));
            } else {
                $this->flash->addMessage('error', __('admin_message_wrong_username_password'));
                return $response->withRedirect($this->router->pathFor('admin.users.login'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_wrong_username_password'));
            return $response->withRedirect($this->router->pathFor('admin.users.login'));
        }
    }

    public function registration($request, $response)
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/users/registration.html'
        );
    }

    /**
     * registrationProcess
     */
    public function registrationProcess($request, $response)
    {
        // Get POST data
        $data = $request->getParsedBody();

        if (!Filesystem::has($_user_file = PATH['site'] . '/accounts/' . Text::safeString($data['username']) . '.json')) {
            Filesystem::createDir(PATH['site'] . '/accounts/');
            if (Filesystem::write(
                PATH['site'] . '/accounts/' . $data['username'] . '.json',
                JsonParser::encode(['username' => Text::safeString($data['username']),
                                            'hashed_password' => password_hash($data['password'], PASSWORD_BCRYPT),
                                            'email' => $data['email'],
                                            'role'  => 'admin',
                                            'state' => 'enabled'])
            )) {
                return $response->withRedirect($this->router->pathFor('admin.users.login'));
            } else {
                return $response->withRedirect($this->router->pathFor('admin.users.registration'));
            }
        } else {
            return $response->withRedirect($this->router->pathFor('admin.users.registration'));
        }
    }

    /**
     * logoutProcess
     */
    public function logoutProcess($request, $response)
    {
        Session::destroy();
        return $response->withRedirect($this->router->pathFor('admin.users.login'));
    }
}
