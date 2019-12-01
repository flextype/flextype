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

        $site_plugin_settings_dir     = PATH['config']['site'] . '/plugins/' . $data['plugin-key'];
        $site_plugin_settings_file    = PATH['config']['site'] . '/plugins/' . $data['plugin-key'] . '/settings.yaml';
        $default_plugin_settings_file = PATH['plugins'] . '/' . $data['plugin-key'] . '/settings.yaml';

        // Update settings
        $site_plugin_settings_file_content = Filesystem::read($site_plugin_settings_file);
        if (trim($site_plugin_settings_file_content) === '') {
            $site_plugin_settings = [];
        } else {
            $site_plugin_settings = $this->parser->decode($site_plugin_settings_file_content, 'yaml');
        }

        Arr::set($site_plugin_settings, 'enabled', ($data['plugin-status'] === 'true'));
        Filesystem::write($site_plugin_settings_file, $this->parser->encode($site_plugin_settings, 'yaml'));

        // Clear doctrine cache
        $this->cache->clear('doctrine');

        // Redirect to plugins index page
        return $response->withRedirect($this->router->pathFor('admin.plugins.index'));
    }

    /**
     * Plugin information
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function information(Request $request, Response $response) : Response
    {
        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Init plugin configs
        $plugin                  = [];
        $plugin_manifest         = [];
        $default_plugin_manifest = [];
        $site_plugin_manifest    = [];

        $default_plugin_manifest_file = PATH['plugins'] . '/' . $id . '/plugin.yaml';
        $site_plugin_manifest_file    = PATH['config']['site'] . '/plugins/' . $id . '/plugin.yaml';

        // Get default plugin manifest content
        $default_plugin_manifest_file_content = Filesystem::read($default_plugin_manifest_file);
        $default_plugin_manifest              = $this->parser->decode($default_plugin_manifest_file_content, 'yaml');

        // Get site plugin manifest content
        $site_plugin_manifest_file_content = Filesystem::read($site_plugin_manifest_file);
        if (trim($site_plugin_manifest_file_content) === '') {
            $site_plugin_manifest = [];
        } else {
            $site_plugin_manifest = $this->parser->decode($site_plugin_manifest_file_content, 'yaml');
        }

        $plugin[$id]['manifest'] = array_merge(
            array_replace_recursive($default_plugin_manifest, $site_plugin_manifest)
        );

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/plugins/information.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_manifest' => $plugin[$id]['manifest'],
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'plugins_information' => [
                        'link' => $this->router->pathFor('admin.plugins.information') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_information'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
            ]
        );
    }

    /**
     * Plugin settings
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settings(Request $request, Response $response) : Response
    {
        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Init plugin configs
        $plugin                  = [];
        $plugin_settings         = [];
        $default_plugin_settings = [];
        $site_plugin_settings    = [];

        $default_plugin_settings_file = PATH['plugins'] . '/' . $id . '/settings.yaml';
        $site_plugin_settings_file    = PATH['config']['site'] . '/plugins/' . $id . '/settings.yaml';

        // Get default plugin settings content
        $default_plugin_settings_file_content = Filesystem::read($default_plugin_settings_file);
        $default_plugin_settings              = $this->parser->decode($default_plugin_settings_file_content, 'yaml');

        // Get site plugin settings content
        $site_plugin_settings_file_content = Filesystem::read($site_plugin_settings_file);
        if (trim($site_plugin_settings_file_content) === '') {
            $site_plugin_settings = [];
        } else {
            $site_plugin_settings = $this->parser->decode($site_plugin_settings_file_content, 'yaml');
        }

        // Merge plugin settings data
        $plugin[$id]['settings'] = array_merge(
            array_replace_recursive($default_plugin_settings, $site_plugin_settings)
        );

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/plugins/settings.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_settings' => $this->parser->encode($plugin[$id]['settings'], 'yaml'),
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'plugins_settings' => [
                        'link' => $this->router->pathFor('admin.plugins.settings') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_settings'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_plugin_settings' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Plugin settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function settingsProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id   = $data['id'];
        $data = $data['data'];

        $site_plugin_settings_dir  = PATH['config']['site'] . '/plugins/' . $id;
        $site_plugin_settings_file = PATH['config']['site'] . '/plugins/' . $id . '/settings.yaml';

        if (Filesystem::write($site_plugin_settings_file, $data)) {
            $this->flash->addMessage('success', __('admin_message_plugin_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_plugin_settings_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.plugins.settings') . '?id=' . $id);
    }
}
