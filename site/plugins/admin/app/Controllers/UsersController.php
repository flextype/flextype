<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property View $view
 * @property Router $router
 * @property Slugify $slugify
 * @property Flash $flash
 */
class UsersController extends Controller
{
    public function profile($request, $response)
    {
        return $this->container->get('view')->render(
            $response,
            'plugins/admin/views/templates/users/profile.html'
        );
    }

    /**
     * Login page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function login(Request $request, Response $response) : Response
    {
        $users = $this->getUsers();

        if ((Session::exists('role') && Session::get('role') == 'admin')) {
            return $response->withRedirect($this->router->pathFor('admin.entries.index'));
        } else {
            if (count($users) > 0) {
                return $this->container->get('view')->render(
                    $response,
                    'plugins/admin/views/templates/users/login.html'
                );
            } else {
                return $response->withRedirect($this->router->pathFor('admin.users.registration'));
            }
        }
    }

    /**
     * Login page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function loginProcess(Request $request, Response $response) : Response
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

    /**
     * Registration page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function registration(Request $request, Response $response) : Response
    {
        $users = $this->getUsers();

        if (count($users) > 0) {
            return $response->withRedirect($this->router->pathFor('admin.users.login'));
        } else {
            if ((Session::exists('role') && Session::get('role') == 'admin')) {
                return $response->withRedirect($this->router->pathFor('admin.entries.index'));
            } else {
                return $this->view->render(
                    $response,
                    'plugins/admin/views/templates/users/registration.html'
                );
            }
        }
    }

    /**
     * Registration page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function registrationProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $data = $request->getParsedBody();

        if (!Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $this->slugify->slugify($data['username']) . '.json')) {
            Filesystem::createDir(PATH['site'] . '/accounts/');
            if (Filesystem::write(
                PATH['site'] . '/accounts/' . $data['username'] . '.json',
                JsonParser::encode(['username' => $this->slugify->slugify($data['username']),
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
     * Logout page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function logoutProcess(Request $request, Response $response) : Response
    {
        Session::destroy();
        return $response->withRedirect($this->router->pathFor('admin.users.login'));
    }

    /**
     * Get Users list
     *
     * @return array
     */
    public function getUsers() : array
    {
        // Get Users Profiles
        $users_list = Filesystem::listContents(PATH['site'] . '/accounts/');

        // Users
        $users = [];

        foreach($users_list as $user) {
            if ($user['type'] == 'file' && $user['extension'] == 'json') {
                $users[$user['basename']] = $user;
            }
        }

        return $users;
    }
}
