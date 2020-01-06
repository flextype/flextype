<?php

declare(strict_types=1);

namespace Flextype;

use function Flextype\Component\I18n\__;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function index(Request $request, Response $response) : Response
    {
        $api_list             = ['delivery' => 'Delivery'];
        $delivery_tokens_list =  Filesystem::listContents(PATH['tokens'] . '/delivery');

        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/index.html',
            [
                'menu_item' => 'api',
                'api_list' => $api_list,
                'delivery_tokens_list' => $delivery_tokens_list,
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ]
            ]
        );
    }

    /**
     * Add token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function add(Request $request, Response $response) : Response
    {

    }

    /**
     * Add new token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function addProccess(Request $request, Response $response) : Response
    {

    }

    /**
     * Rename token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function rename(Request $request, Response $response) : Response
    {

    }

    /**
     * Rename token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function renameProccess(Request $request, Response $response) : Response
    {

    }

    /**
     * Edit token page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function edit(Request $request, Response $response) : Response
    {

    }

    /**
     * Edit token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function editProccess(Request $request, Response $response) : Response
    {

    }

    /**
     * Delete token - process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function deleteProccess(Request $request, Response $response) : Response
    {

    }
}
