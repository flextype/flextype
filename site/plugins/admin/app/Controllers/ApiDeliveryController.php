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

class ApiDeliveryController extends Controller
{
    /**
     * Delivery Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/templates/system/api/delivery/index.html',
            [
                'menu_item' => 'api',
                'api_list' => ['entries' => __('admin_entries'), 'images' => __('admin_images'), 'registry' => __('admin_registry')],
                'links' =>  [
                    'api' => [
                        'link' => $this->router->pathFor('admin.api.index'),
                        'title' => __('admin_api')
                    ],
                    'api_delivery' => [
                        'link' => $this->router->pathFor('admin.api_delivery.index'),
                        'title' => __('admin_delivery'),
                        'active' => true,
                    ],
                ],
            ]
        );
    }
}
