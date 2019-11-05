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
        $plugin_settings = $this->parser->decode(Filesystem::read(PATH['plugins'] . '/' . $data['plugin-key'] . '/' . 'settings.yaml'), 'yaml');
        Arr::set($plugin_settings, 'enabled', ($data['plugin-status'] === 'true'));
        Filesystem::write(PATH['plugins'] . '/' . $data['plugin-key'] . '/' . 'settings.yaml', $this->parser->encode($plugin_settings, 'yaml'));

        // Clear doctrine cache
        $this->cache->clear('doctrine');

        // Redirect to plugins index page
        return $response->withRedirect($this->router->pathFor('admin.plugins.index'));
    }

    /**
     * Edit plugin
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function edit(Request $request, Response $response) : Response
    {
        // Get Plugin ID
        $id = $request->getQueryParams()['id'];

        // Init plugin configs
        $plugin                  = [];
        $plugin_settings         = [];
        $plugin_manifest         = [];
        $default_plugin_settings = [];
        $site_plugin_settings    = [];
        $default_plugin_manifest = [];
        $site_plugin_manifest    = [];

        $default_plugin_settings_file = PATH['plugins'] . '/' . $id . '/settings.yaml';
        $default_plugin_manifest_file = PATH['plugins'] . '/' . $id . '/plugin.yaml';
        $site_plugin_settings_file    = PATH['config']['site'] . '/plugins/' . $id . '/settings.yaml';
        $site_plugin_manifest_file    = PATH['config']['site'] . '/plugins/' . $id . '/plugin.yaml';

        if (Filesystem::has($default_plugin_settings_file)) {
            $default_plugin_settings_file_content = Filesystem::read($default_plugin_settings_file);
            $default_plugin_settings              = $this->parser->decode($default_plugin_settings_file_content, 'yaml');
        }

        if (Filesystem::has($site_plugin_settings_file)) {
            $site_plugin_settings_file_content = Filesystem::read($site_plugin_settings_file);
            $site_plugin_settings              = $this->parser->decode($site_plugin_settings_file_content, 'yaml');
        }

        if (Filesystem::has($default_plugin_manifest_file)) {
            $default_plugin_manifest_file_content = Filesystem::read($default_plugin_manifest_file);
            $default_plugin_manifest              = $this->parser->decode($default_plugin_manifest_file_content, 'yaml');
        }

        if (Filesystem::has($site_plugin_manifest_file)) {
            $site_plugin_manifest_file_content = Filesystem::read($site_plugin_manifest_file);
            $site_plugin_manifest              = $this->parser->decode($site_plugin_manifest_file_content, 'yaml');
        }

        $plugin[$id]['manifest'] = array_merge(
            array_replace_recursive($default_plugin_manifest, $site_plugin_manifest)
        );

        $plugin[$id]['settings'] = array_merge(
            array_replace_recursive($default_plugin_settings, $site_plugin_settings)
        );

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/extends/plugins/edit.html',
            [
                'menu_item' => 'plugins',
                'id' => $id,
                'plugin_manifest' => $plugin[$id]['manifest'],
                'plugin_settings' => $this->parser->encode($plugin[$id]['settings'], 'yaml'),
                'links' =>  [
                    'plugins' => [
                        'link' => $this->router->pathFor('admin.plugins.index'),
                        'title' => __('admin_plugins'),
                        'attributes' => ['class' => 'navbar-item'],
                    ],
                    'fieldsets_editor' => [
                        'link' => $this->router->pathFor('admin.plugins.edit') . '?id=' . $request->getQueryParams()['id'],
                        'title' => __('admin_plugin'),
                        'attributes' => ['class' => 'navbar-item active'],
                    ],
                ],
                'buttons' => [
                    'save_entry' => [
                        'link' => 'javascript:;',
                        'title' => __('admin_save'),
                        'attributes' => ['class' => 'js-save-form-submit float-right btn'],
                    ],
                ],
            ]
        );
    }

    /**
     * Edit plugin process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     */
    public function editProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        $id   = $data['id'];
        $data = $data['data'];

        $site_plugin_settings_dir  = PATH['config']['site'] . '/plugins/' . $id;
        $site_plugin_settings_file = PATH['config']['site'] . '/plugins/' . $id . '/settings.yaml';

        if (Filesystem::has($site_plugin_settings_file)) {
            Filesystem::write($site_plugin_settings_file, $data);
            $this->flash->addMessage('success', __('admin_message_plugin_settings_saved'));
        } else {
            ! Filesystem::has($site_plugin_settings_dir) and Filesystem::createDir($site_plugin_settings_dir);
            Filesystem::write($site_plugin_settings_file, $data);
            $this->flash->addMessage('success', __('admin_message_plugin_settings_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.plugins.edit') . '?id=' . $id);
    }
}
