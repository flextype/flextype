<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Registry $registry
 */
class PluginsController extends Controller
{
    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/plugins/index.html',
            [
                'plugins_list' => $this->registry->get('plugins'),
                'menu_item' => 'plugins',
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' =>  [
                    'plugins_get_more' => [
                        'link' => 'https://github.com/flextype/plugins',
                        'title' => __('admin_get_more_plugins'),
                        'attributes' => ['class' => 'float-right btn', 'target' => '_blank'],
                    ],
                ],
            ]
        );
    }

    /**
     * Сhange plugin status process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function pluginStatusProcess(Request $request, Response $response) : Response
    {
        // Get data from the request
        $data = $request->getParsedBody();

        // Update settings
        $plugin_settings = Parser::decode(Filesystem::read(PATH['plugins'] . '/' . $data['plugin-key'] . '/' . 'settings.yaml'), 'yaml');
        Arr::set($plugin_settings, 'enabled', ($data['plugin-status'] === 'true'));
        Filesystem::write(PATH['plugins'] . '/' . $data['plugin-key'] . '/' . 'settings.yaml', Parser::encode($plugin_settings, 'yaml'));

        // Clear doctrine cache
        $this->cache->clear('doctrine');

        // Redirect to plugins index page
        return $response->withRedirect($this->router->pathFor('admin.plugins.index'));
    }
}
