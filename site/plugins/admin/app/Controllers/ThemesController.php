<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Text\Text;
use function Flextype\Component\I18n\__;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @property View $view
 * @property Router $router
 * @property Cache $cache
 * @property Themes $themes
 * @property Slugify $slugify
 */
class ThemesController extends Controller
{

        /**
         * Index page
         *
         * @param Request  $request  PSR7 request
         * @param Response $response PSR7 response
         *
         * @return Response
         */
        public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
        {
            $themes = [];
            foreach (Filesystem::listContents(PATH['themes']) as $theme) {
                if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . 'theme.json')) {
                    $themes[$theme['dirname']] = $theme['dirname'];
                }
            }

            return $this->view->render(
                $response,
                'plugins/admin/views/templates/extends/themes/index.html',
                [
                'menu_item' => 'themes',
                'themes_list' => $themes,
                'links' =>  [
                                'themes' => [
                                    'link' => $this->router->pathFor('admin.themes.index'),
                                    'title' => __('admin_themes'),
                                    'attributes' => ['class' => 'navbar-item active']
                                ],
                            ],
                'buttons' => [
                                'templates_create' => [
                                    'link' => $this->router->pathFor('admin.templates.add'),
                                    'title' => __('admin_create_new_template'),
                                    'attributes' => ['class' => 'float-right btn']
                                ],
                            ]
            ]
            );
        }
}
