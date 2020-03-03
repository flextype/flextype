<?php

declare(strict_types=1);

namespace Flextype;

use DateTime;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Date\Date;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function array_merge;
use function explode;
use function Flextype\Component\I18n\__;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Entries $entries
 * @property Plugins $plugins
 * @property Registry $registry
 * @property Flash $flash
 */
class SettingsController extends Controller
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
            'plugins/admin/templates/system/settings/index.html',
            [
                'data' => Filesystem::read(PATH['config']['site'] . '/settings.yaml'),
                'menu_item' => 'settings',
                'links' => [
                    'settings' => [
                        'link' => $this->router->pathFor('admin.settings.index'),
                        'title' => __('admin_settings'),
                        'active' => true
                    ],
                ],
                'buttons'  => [
                    'save' => [
                        'link'       => 'javascript:;',
                        'title'      => __('admin_save'),
                        'type' => 'action'
                    ],
                ],
            ]
        );
    }

    /**
     * Update settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function updateSettingsProcess(Request $request, Response $response) : Response
    {
        $post_data = $request->getParsedBody();

        if (Filesystem::write(PATH['config']['site'] . '/settings.yaml', $post_data['data'])) {
            $this->flash->addMessage('success', __('admin_message_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_settings_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

    /**
     * Return date formats allowed
     *
     * @return array
     */
    public function dateFormats() : array
    {
        $now = new DateTime();

        return [
            'd-m-Y H:i' => $now->format('d-m-Y H:i'),
            'Y-m-d H:i' => $now->format('Y-m-d H:i'),
            'm/d/Y h:i a' => $now->format('m/d/Y h:i a'),
            'H:i d-m-Y' => $now->format('H:i d-m-Y'),
            'h:i a m/d/Y' => $now->format('h:i a m/d/Y'),
        ];
    }

    /**
     * Return display date formats allowed
     *
     * @return array
     */
    public function displayDateFormats() : array
    {
        $now = new DateTime();

        return [
            'F jS \\a\\t g:ia' => $now->format('F jS \\a\\t g:ia'),
            'l jS \\of F g:i A' => $now->format('l jS \\of F g:i A'),
            'D, d M Y G:i:s' => $now->format('m/d/Y h:i a'),
            'd-m-y G:i' => $now->format('d-m-y G:i'),
            'jS M Y' => $now->format('jS M Y'),
        ];
    }
}
