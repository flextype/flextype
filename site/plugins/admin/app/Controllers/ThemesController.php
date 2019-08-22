<?php

declare(strict_types=1);

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Filesystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function count;
use function Flextype\Component\I18n\__;
use function is_array;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Themes $themes
 */
class ThemesController extends Controller
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
            'plugins/admin/views/templates/extends/themes/index.html',
            [
                'menu_item' => 'themes',
                'themes_list' => $this->registry->get('themes'),
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'themes_get_more' => [
                        'link' => 'https://github.com/flextype/themes',
                        'title' => __('admin_get_more_themes'),
                        'attributes' => ['class' => 'float-right btn', 'target' => '_blank'],
                    ],
                ],
            ]
        );
    }

    /**
     * Сhange theme status process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function activateProcess(Request $request, Response $response) : Response
    {
        // Get data from the request
        $data = $request->getParsedBody();

        // Update current theme settings
        $theme_settings = Parser::decode(Filesystem::read(PATH['themes'] . '/' . $data['theme-id'] . '/' . 'settings.yaml'), 'yaml');
        Arr::set($theme_settings, 'enabled', ($data['theme-status'] === 'true'));
        Filesystem::write(PATH['themes'] . '/' . $data['theme-id'] . '/' . 'settings.yaml', Parser::encode($theme_settings, 'yaml'));

        // Get themes list
        $themes_list = $this->themes->getThemes();

        // Deactivate all others themes
        if (is_array($themes_list) && count($themes_list) > 0) {
            foreach ($themes_list as $theme) {
                if ($theme['dirname'] === $data['theme-id']) {
                    continue;
                }

                if (! Filesystem::has($theme_settings_file = PATH['themes'] . '/' . $theme['dirname'] . '/settings.yaml')) {
                    continue;
                }

                $theme_settings = Parser::decode(Filesystem::read($theme_settings_file), 'yaml');
                Arr::set($theme_settings, 'enabled', false);
                Filesystem::write($theme_settings_file, Parser::encode($theme_settings, 'yaml'));
            }
        }

        // Update theme in the site settings
        $settings = Parser::decode(Filesystem::read(PATH['config']['site'] . '/settings.yaml'), 'yaml');
        Arr::set($settings, 'theme', $data['theme-id']);
        Filesystem::write(PATH['config']['site'] . '/settings.yaml', Parser::encode($settings, 'yaml'));

        // clear cache
        $this->cache->clear('doctrine');

        // Redirect to themes index page
        return $response->withRedirect($this->router->pathFor('admin.themes.index'));
    }
}
