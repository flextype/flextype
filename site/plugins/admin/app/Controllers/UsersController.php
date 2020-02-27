<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use function count;
use function date;
use function Flextype\Component\I18n\__;
use function password_hash;
use function password_verify;
use function time;
use function trim;
use const PASSWORD_BCRYPT;

/**
 * @property View $view
 * @property Router $router
 * @property Slugify $slugify
 * @property Flash $flash
 */
class UsersController extends Controller
{
    /**
     * Login page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function login(Request $request, Response $response) : Response
    {
        $users = $this->getUsersList();

        if ((Session::exists('role') && Session::get('role') === 'admin')) {
            return $response->withRedirect($this->router->pathFor('admin.entries.index'));
        }

        if (count($users) > 0) {
            return $this->container->get('view')->render(
                $response,
                'plugins/admin/templates/users/login.html'
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

        if (Filesystem::has($_user_file = PATH['accounts'] . '/' . $data['username'] . '/profile.yaml')) {
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
        $users = $this->getUsersList();

        if (count($users) > 0) {
            return $response->withRedirect($this->router->pathFor('admin.users.login'));
        }

        if ((Session::exists('role') && Session::get('role') === 'admin')) {
            return $response->withRedirect($this->router->pathFor('admin.entries.index'));
        }

        return $this->view->render(
            $response,
            'plugins/admin/templates/users/installation.html'
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

        if (! Filesystem::has($_user_file = PATH['accounts'] . '/' . $this->slugify->slugify($data['username']) . '/profile.yaml')) {
            // Generate UUID
            $uuid = Uuid::uuid4()->toString();

            // Get time
            $time = date($this->registry->get('flextype.date_format'), time());

            // Create accounts directory and account
            Filesystem::createDir(PATH['accounts'] . '/' . $this->slugify->slugify($data['username']));

            // Create admin account
            if (Filesystem::write(
                PATH['accounts'] . '/' . $this->slugify->slugify($data['username']) . '/profile.yaml',
                $this->parser->encode([
                    'username' => $this->slugify->slugify($data['username']),
                    'hashed_password' => password_hash($data['password'], PASSWORD_BCRYPT),
                    'email' => $data['email'],
                    'role'  => 'admin',
                    'state' => 'enabled',
                    'uuid' => $uuid,
                ], 'yaml')
            )) {

                // Update default entry
                $this->entries->update('home', ['created_by' => $uuid, 'published_by' => $uuid, 'published_at' => $time, 'created_at' => $time]);

                // Create default entries delivery token
                $api_delivery_entries_token = bin2hex(random_bytes(16));
                $api_delivery_entries_token_dir_path  = PATH['tokens'] . '/delivery/entries/' . $api_delivery_entries_token;
                $api_delivery_entries_token_file_path = $api_delivery_entries_token_dir_path . '/token.yaml';

                if (! Filesystem::has($api_delivery_entries_token_dir_path)) Filesystem::createDir($api_delivery_entries_token_dir_path);

                Filesystem::write(
                    $api_delivery_entries_token_file_path,
                    $this->parser->encode([
                        'title' => 'Default',
                        'icon' => 'fas fa-database',
                        'limit_calls' => (int) 0,
                        'calls' => (int) 0,
                        'state' => 'enabled',
                        'uuid' => $uuid,
                        'created_by' => $uuid,
                        'created_at' => $time,
                        'updated_by' => $uuid,
                        'updated_at' => $time,
                    ], 'yaml')
                );

                // Create default images delivery token
                $api_delivery_images_token = bin2hex(random_bytes(16));
                $api_delivery_images_token_dir_path  = PATH['tokens'] . '/delivery/images/' . $api_delivery_images_token;
                $api_delivery_images_token_file_path = $api_delivery_images_token_dir_path . '/token.yaml';

                if (! Filesystem::has($api_delivery_images_token_dir_path)) Filesystem::createDir($api_delivery_images_token_dir_path);

                Filesystem::write(
                    $api_delivery_images_token_file_path,
                    $this->parser->encode([
                        'title' => 'Default',
                        'icon' => 'far fa-images',
                        'limit_calls' => (int) 0,
                        'calls' => (int) 0,
                        'state' => 'enabled',
                        'uuid' => $uuid,
                        'created_by' => $uuid,
                        'created_at' => $time,
                        'updated_by' => $uuid,
                        'updated_at' => $time,
                    ], 'yaml')
                );

                // Create default registry delivery token
                $api_delivery_registry_token = bin2hex(random_bytes(16));
                $api_delivery_registry_token_dir_path  = PATH['tokens'] . '/delivery/registry/' . $api_delivery_registry_token;
                $api_delivery_registry_token_file_path = $api_delivery_registry_token_dir_path . '/token.yaml';

                if (! Filesystem::has($api_delivery_registry_token_dir_path)) Filesystem::createDir($api_delivery_registry_token_dir_path);

                Filesystem::write(
                    $api_delivery_registry_token_file_path,
                    $this->parser->encode([
                        'title' => 'Default',
                        'icon' => 'fas fa-archive',
                        'limit_calls' => (int) 0,
                        'calls' => (int) 0,
                        'state' => 'enabled',
                        'uuid' => $uuid,
                        'created_by' => $uuid,
                        'created_at' => $time,
                        'updated_by' => $uuid,
                        'updated_at' => $time,
                    ], 'yaml')
                );

                // Set Default API's tokens
                $custom_flextype_settings_file_path = PATH['config']['site'] . '/settings.yaml';
                $custom_flextype_settings_file_data = $this->parser->decode(Filesystem::read($custom_flextype_settings_file_path), 'yaml');

                $custom_flextype_settings_file_data['api']['images']['default_token']  = $api_delivery_images_token;
                $custom_flextype_settings_file_data['api']['entries']['default_token'] = $api_delivery_entries_token;
                $custom_flextype_settings_file_data['api']['registry']['default_token'] = $api_delivery_registry_token;

                Filesystem::write($custom_flextype_settings_file_path, $this->parser->encode($custom_flextype_settings_file_data, 'yaml'));

                // Create uploads dir for default entries
                if (! Filesystem::has(PATH['uploads'] . '/entries/home/')) {
                    Filesystem::createDir(PATH['uploads'] . '/entries/home/');
                }

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
    public function getUsersList() : array
    {
        // Get Users Profiles
        $users_list = Filesystem::listContents(PATH['accounts']);

        // Users
        $users = [];

        foreach ($users_list as $user) {
            if ($user['type'] === 'dir' && Filesystem::has($user['path'] . '/profile.yaml')) {
                $users[$user['dirname']] = $user;
            }
        }

        return $users;
    }
}
