<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use const PASSWORD_BCRYPT;
use function count;
use function Flextype\Component\I18n\__;
use function password_hash;
use function password_verify;
use function trim;

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
     */
    public function login(Request $request, Response $response) : Response
    {
        $users = $this->getUsers();

        if ((Session::exists('role') && Session::get('role') === 'admin')) {
            return $response->withRedirect($this->router->pathFor('admin.entries.index'));
        }

        if (count($users) > 0) {
            return $this->container->get('view')->render(
                $response,
                'plugins/admin/views/templates/users/login.html'
            );
        }

        return $response->withRedirect($this->router->pathFor('admin.users.installation'));
    }

    /**
     * Login page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function loginProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        if (Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $data['username'] . '.yaml')) {
            $user_file = $this->parser->decode(Filesystem::read($_user_file), 'yaml', false);
            if (password_verify(trim($data['password']), $user_file['hashed_password'])) {
                Session::set('username', $user_file['username']);
                Session::set('role', $user_file['role']);
                Session::set('uuid', $user_file['uuid']);

                return $response->withRedirect($this->router->pathFor('admin.entries.index'));
            }

            $this->flash->addMessage('error', __('admin_message_wrong_username_password'));

            return $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        $this->flash->addMessage('error', __('admin_message_wrong_username_password'));

        return $response->withRedirect($this->router->pathFor('admin.users.login'));
    }

    /**
     * Installation page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function installation(Request $request, Response $response) : Response
    {
        $users = $this->getUsers();

        if (count($users) > 0) {
            return $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        if ((Session::exists('role') && Session::get('role') === 'admin')) {
            return $response->withRedirect($this->router->pathFor('admin.entries.index'));
        }

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/users/installation.html'
        );
    }

    /**
     * Installation page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function installationProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $data = $request->getParsedBody();

        if (! Filesystem::has($_user_file = PATH['site'] . '/accounts/' . $this->slugify->slugify($data['username']) . '.yaml')) {

            // Generate UUID
            $uuid = Uuid::uuid4()->toString();

            // Get time
            $time = date($this->registry->get('settings.date_format'), time());

            // Create accounts directory and account
            Filesystem::createDir(PATH['site'] . '/accounts/');

            // Create admin account
            if (Filesystem::write(
                        PATH['site'] . '/accounts/' . $data['username'] . '.yaml',
                        $this->parser->encode([
                            'username' => $this->slugify->slugify($data['username']),
                            'hashed_password' => password_hash($data['password'], PASSWORD_BCRYPT),
                            'email' => $data['email'],
                            'role'  => 'admin',
                            'state' => 'enabled',
                            'uuid' => $uuid,
                        ], 'yaml')
                    )) {

                // Update default flextype entries
                $this->entries->update('home', ['created_by' => $uuid, 'published_by' => $uuid, 'published_at' => $time, 'created_at' => $time]);

                return $response->withRedirect($this->router->pathFor('admin.users.login'));
            }

            return $response->withRedirect($this->router->pathFor('admin.users.installation'));
        }

        return $response->withRedirect($this->router->pathFor('admin.users.installation'));
    }

    /**
     * Logout page process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
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

        foreach ($users_list as $user) {
            if ($user['type'] !== 'file' || $user['extension'] !== 'yaml') {
                continue;
            }

            $users[$user['basename']] = $user;
        }

        return $users;
    }

}
