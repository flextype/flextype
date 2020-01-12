<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use function bin2hex;
use function date;
use function Flextype\Component\I18n\__;
use function random_bytes;
use function time;

class ApiController extends Controller
{
    /**
     * Index page for API's
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/index.html',
            [
                'menu_item' => 'api',
                'api_list' => ['delivery' => 'Delivery'],
                'links' =>  [
                    'api' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Index page for tokens
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function tokensIndex(Request $request, Response $response) : Response
    {
        $api = $request->getQueryParams()['api'];

        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/delivery/index.html',
            [
                'menu_item' => 'api',
                'api' => $api,
                'delivery_tokens_list' => Filesystem::listContents(PATH['tokens'] . '/delivery'),
                'links' =>  [
                    'api' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'api_tokens' => [
                        'link' => $this->router->pathFor('admin.api_tokens.index') . '?api=' . $api,
                        'title' => __('admin_' . $api),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'api_tokens_add' => [
                        'link' => $this->router->pathFor('admin.api_tokens.add') . '?api=' . $api,
                        'title' => __('admin_create_new_' . $api . '_token'),
                        'attributes' => ['class' => 'float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function add(Request $request, Response $response) : Response
    {
        $api = $request->getQueryParams()['api'];

        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/delivery/add.html',
            [
                'menu_item' => 'api',
                'api' => $api,
                'links' =>  [
                    'api' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'api_tokens' => [
                        'link' => $this->router->pathFor('admin.api_tokens.index') . '?api=' . $api,
                        'title' => __('admin_' . $api),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'api_tokens_add' => [
                        'link' => $this->router->pathFor('admin.api_tokens.add') . '?api=' . $api,
                        'title' => __('admin_create_new_' . $api . '_token'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Add new token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function addProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        // Generate API token
        $api_token = bin2hex(random_bytes(16));

        $api_token_dir_path  = PATH['tokens'] . '/' . $post_data['api'] . '/' . $api_token;
        $api_token_file_path = $api_token_dir_path . '/' . 'token.yaml';

        if (! Filesystem::has($api_token_dir_path)) {
            // Generate UUID
            $uuid = Uuid::uuid4()->toString();

            // Get time
            $time = date($this->registry->get('settings.date_format'), time());

            // Create API Token directory
            Filesystem::createDir($api_token_dir_path);

            // Create API Token account
            if (Filesystem::write(
                $api_token_file_path,
                $this->parser->encode([
                    'title' => $post_data['title'],
                    'icon' => $post_data['icon'],
                    'limit_calls' => (int) $post_data['limit_calls'],
                    'limit_rate' => $post_data['limit_rate'],
                    'state' => $post_data['state'],
                    'uuid' => $uuid,
                    'created_by' => Session::get('uuid'),
                    'created_at' => $time,
                    'updated_by' => Session::get('uuid'),
                    'updated_at' => $time,
                ], 'yaml')
            )) {
                $this->flash->addMessage('success', __('admin_message_' . $post_data['api'] . '_api_token_created'));
            } else {
                $this->flash->addMessage('error', __('admin_message_' . $post_data['api'] . '_api_token_was_not_created'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $post_data['api'] . '_api_token_was_not_created'));
        }

        return $response->withRedirect($this->router->pathFor('admin.api_tokens.index') . '?api=' . $post_data['api']);
    }

    /**
     * Edit token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        $api            = $request->getQueryParams()['api'];
        $api_token      = $request->getQueryParams()['api_token'];
        $api_token_data = $this->parser->decode(Filesystem::read(PATH['tokens'] . '/' . $api . '/' . $api_token . '/token.yaml'), 'yaml');

        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/delivery/edit.html',
            [
                'menu_item' => 'api',
                'api' => $api,
                'api_token' => $api_token,
                'api_token_data' => $api_token_data,
                'links' =>  [
                    'api' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'api_tokens' => [
                        'link' => $this->router->pathFor('admin.api_tokens.index') . '?api=' . $api,
                        'title' => __('admin_' . $api),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'api_tokens_add' => [
                        'link' => $this->router->pathFor('admin.api_tokens.add') . '?api=' . $api,
                        'title' => __('admin_create_new_' . $api . '_token'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Edit token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        $api_token_dir_path  = PATH['tokens'] . '/' . $post_data['api'] . '/' . $post_data['api_token'];
        $api_token_file_path = $api_token_dir_path . '/' . 'token.yaml';

        // Update API Token File
        if (Filesystem::has($api_token_file_path)) {
            if (Filesystem::write(
                $api_token_file_path,
                $this->parser->encode([
                    'title' => $post_data['title'],
                    'icon' => $post_data['icon'],
                    'limit_calls' => (int) $post_data['limit_calls'],
                    'limit_rate' => $post_data['limit_rate'],
                    'state' => $post_data['state'],
                    'uuid' => $post_data['uuid'],
                    'created_by' => $post_data['created_by'],
                    'created_at' => $post_data['created_at'],
                    'updated_by' => Session::get('uuid'),
                    'updated_at' => date($this->registry->get('settings.date_format'), time()),
                ], 'yaml')
            )) {
                $this->flash->addMessage('success', __('admin_message_' . $post_data['api'] . '_api_token_updated'));
            }
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $post_data['api'] . '_api_token_was_not_updated'));
        }

        return $response->withRedirect($this->router->pathFor('admin.api_tokens.index') . '?api=' . $data['api']);
    }

    /**
     * Delete token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function deleteProcess(Request $request, Response $response) : Response
    {
        // Get POST data
        $post_data = $request->getParsedBody();

        $api_token_dir_path = PATH['tokens'] . '/' . $post_data['api'] . '/' . $post_data['api_token'];

        if (Filesystem::deleteDir($api_token_dir_path)) {
            $this->flash->addMessage('success', __('admin_message_' . $post_data['api'] . '_api_token_deleted'));
        } else {
            $this->flash->addMessage('error', __('admin_message_' . $post_data['api'] . '_api_token_was_not_deleted'));
        }

        return $response->withRedirect($this->router->pathFor('admin.api_tokens.index') . '?api=' . $post_data['api']);
    }
}
