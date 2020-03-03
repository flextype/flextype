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
                        'active' => true,
                    ],
                ],
            ]
        );
    }
}
