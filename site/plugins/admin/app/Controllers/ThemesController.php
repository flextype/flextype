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

        $site_theme_settings_dir     = PATH['config']['site'] . '/themes/';
        $site_theme_settings_file    = PATH['config']['site'] . '/themes/' . $data['theme-id'] . '/settings.yaml';
        $default_theme_settings_file = PATH['themes'] . '/' . $data['theme-id'] . '/settings.yaml';

        // Update current theme settings
        $site_theme_settings_file_content = Filesystem::read($site_theme_settings_file);
        if (trim($site_theme_settings_file_content) === '') {
            $site_theme_settings = [];
        } else {
            $site_theme_settings = $this->parser->decode($site_theme_settings_file_content, 'yaml');
        }

        Arr::set($site_theme_settings, 'enabled', ($data['theme-status'] === 'true'));
        Filesystem::write($site_theme_settings_file, $this->parser->encode($site_theme_settings, 'yaml'));

        // Get themes list
        $themes_list = $this->themes->getThemes();

        // Deactivate all others themes
        if (is_array($themes_list) && count($themes_list) > 0) {
            foreach ($themes_list as $theme) {
                if ($theme['dirname'] === $data['theme-id']) {
                    continue;
                }

                if (! Filesystem::has($theme_settings_file = $site_theme_settings_dir . $theme['dirname'] . '/settings.yaml')) {
                    continue;
                }


                if (($content = Filesystem::read($theme_settings_file)) === false) {
                    throw new RuntimeException('Load file: ' . $theme_settings_file . ' - failed!');
                } else {
                    if (trim($content) === '') {
                        $theme_settings = [];
                    } else {
                        $theme_settings = $this->parser->decode($content, 'yaml');
                    }
                }

                Arr::set($theme_settings, 'enabled', false);
                Filesystem::write($theme_settings_file, $this->parser->encode($theme_settings, 'yaml'));
            }
        }

        // Update theme in the site settings
        $site_settings_file_path = PATH['config']['site'] . '/settings.yaml';
        if (($content = Filesystem::read($site_settings_file_path)) === false) {
            throw new RuntimeException('Load file: ' . $site_settings_file_path . ' - failed!');
        } else {
            if (trim($content) === '') {
                $site_settings = [];
            } else {
                $site_settings = $this->parser->decode($content, 'yaml');
            }
        }

        Arr::set($site_settings, 'theme', $data['theme-id']);
        Filesystem::write($site_settings_file_path, $this->parser->encode($site_settings, 'yaml'));

        // clear cache
        $this->cache->clear('doctrine');

        // Redirect to themes index page
        return $response->withRedirect($this->router->pathFor('admin.themes.index'));
    }

    /**
     * Theme information
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        // Get Theme ID
        $id = $request->getQueryParams()['id'];

        // Init theme configs
        $theme                  = [];
        $theme_manifest         = [];
        $default_theme_manifest = [];
        $site_theme_manifest    = [];

        $default_theme_manifest_file = PATH['themes'] . '/' . $id . '/theme.yaml';
        $site_theme_manifest_file    = PATH['config']['site'] . '/themes/' . $id . '/theme.yaml';

        // Get default theme manifest content
        $default_theme_manifest_file_content = Filesystem::read($default_theme_manifest_file);
        $default_theme_manifest              = $this->parser->decode($default_theme_manifest_file_content, 'yaml');

        // Get site theme manifest content
        $site_theme_manifest_file_content = Filesystem::read($site_theme_manifest_file);
        if (trim($site_theme_manifest_file_content) === '') {
            $site_theme_manifest = [];
        } else {
            $site_theme_manifest = $this->parser->decode($site_theme_manifest_file_content, 'yaml');
        }

        $theme[$id]['manifest'] = array_merge(
            array_replace_recursive($default_theme_manifest, $site_theme_manifest)
        );

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/information.html',
            [
                'menu_item' => 'themes',
                'id' => $id,
                'theme_manifest' => $theme[$id]['manifest'],
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'themes_information' => [
                        'link' => $this->router->pathFor('admin.themes.information') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_information'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Theme settings
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settings(Request $request, Response $response) : Response
    {
        // Get Theme ID
        $id = $request->getQueryParams()['id'];

        // Init theme configs
        $theme                  = [];
        $theme_settings         = [];
        $default_theme_settings = [];
        $site_theme_settings    = [];

        $default_theme_settings_file = PATH['themes'] . '/' . $id . '/settings.yaml';
        $site_theme_settings_file    = PATH['config']['site'] . '/themes/' . $id . '/settings.yaml';

        // Get default theme settings content
        $default_theme_settings_file_content = Filesystem::read($default_theme_settings_file);
        $default_theme_settings              = $this->parser->decode($default_theme_settings_file_content, 'yaml');

        // Get site plugin settings content
        $site_theme_settings_file_content = Filesystem::read($site_theme_settings_file);
        if (trim($site_theme_settings_file_content) === '') {
            $site_theme_settings = [];
        } else {
            $site_theme_settings = $this->parser->decode($site_theme_settings_file_content, 'yaml');
        }

        $theme[$id]['settings'] = array_merge(
            array_replace_recursive($default_theme_settings, $site_theme_settings)
        );

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/themes/settings.html',
            [
                'menu_item' => 'themes',
                'id' => $id,
                'theme_settings' => $this->parser->encode($theme[$id]['settings'], 'yaml'),
                'links' =>  [
                    'themes' => [
                        'link' => $this->router->pathFor('admin.themes.index'),
                        'title' => __('admin_themes'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'themes_settings' => [
                        'link' => $this->router->pathFor('admin.themes.settings') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_settings'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_theme_settings' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Theme settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settingsProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id   = $data['id'];
        $data = $data['data'];

        $site_theme_settings_dir  = PATH['config']['site'] . '/themes/' . $id;
        $site_theme_settings_file = PATH['config']['site'] . '/themes/' . $id . '/settings.yaml';

        if (Filesystem::write($site_theme_settings_file, $data)) {
            $this->flash->addMessage('success', __('admin_message_theme_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_theme_settings_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.themes.settings') . '?id=' . $id);
    }
}
