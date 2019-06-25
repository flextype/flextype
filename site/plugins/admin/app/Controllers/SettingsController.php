<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Arr\Arr;
use function Flextype\Component\I18n\__;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
     *
     * @return Response
     */
    public function index(/** @scrutinizer ignore-unused */ Request $request, Response $response) : Response
    {
        $entries = [];
        foreach ($this->entries->fetchAll('', 'date', 'DESC') as $entry) {
            $entries[$entry['slug']] = $entry['title'];
        }

        $themes = [];
        foreach (Filesystem::listContents(PATH['themes']) as $theme) {
            if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . 'theme.json')) {
                $themes[$theme['dirname']] = $theme['dirname'];
            }
        }

        $available_locales = Filesystem::listContents(PATH['plugins'] . '/admin/lang/');
        $system_locales = $this->plugins->getLocales();
        $locales = [];
        foreach ($available_locales as $locale) {
            if ($locale['type'] == 'file' && $locale['extension'] == 'json') {
                $locales[$locale['basename']] = $system_locales[$locale['basename']]['nativeName'];
            }
        }

        $cache_driver = ['auto' => 'Auto Detect',
                            'file' => 'File',
                            'apcu' => 'APCu',
                            'wincache' => 'WinCache',
                            'memcached' => 'Memcached',
                            'redis' => 'Redis',
                            'sqlite3' => 'SQLite3',
                            'zend' => 'Zend',
                            'array' => 'Array'];

        $image_driver = ['gd' => 'gd',
                        'imagick' => 'imagick'];

        $whoops_editor = ['emacs' => 'Emacs',
                          'idea' => 'IDEA',
                          'macvim' => 'MacVim',
                          'phpstorm' => 'PhpStorm (macOS only)',
                          'sublime' => 'Sublime Text',
                          'textmate' => 'Textmate',
                          'xdebug' => 'xDebug',
                          'vscode' => 'VSCode',
                          'atom' => 'Atom',
                          'espresso' => 'Espresso'];

        return $this->view->render(
            $response,
            'plugins/admin/views/templates/system/settings/index.html',
            [
                                        'timezones' => Date::timezones(),
                                        'cache_driver' => $cache_driver,
                                        'locales' => $locales,
                                        'entries' => $entries,
                                        'themes' => $themes,
                                        'image_driver' => $image_driver,
                                        'whoops_editor' => $whoops_editor,
                                        'menu_item' => 'settings',
                                        'links' => [
                                            'settings' => [
                                                'link' => $this->router->pathFor('admin.settings.index'),
                                                'title' => __('admin_settings'),
                                                'attributes' => ['class' => 'navbar-item active']
                                            ]
                                        ],
                                        'buttons'  => [
                                                                    'save' => [
                                                                                        'link'       => 'javascript:;',
                                                                                        'title'      => __('admin_save'),
                                                                                        'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                                                                    ]
                                                            ]
                                    ]
        );
    }

    /**
     * Update settings process
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     *
     * @return Response
     */
    public function updateSettingsProcess(Request $request, Response $response) : Response
    {
        $data = $request->getParsedBody();

        Arr::delete($data, 'csrf_name');
        Arr::delete($data, 'csrf_value');
        Arr::delete($data, 'action');

        Arr::set($data, 'errors.display', ($data['errors']['display'] == '1' ? true : false));
        Arr::set($data, 'cache.enabled', ($data['cache']['enabled'] == '1' ? true : false));
        Arr::set($data, 'slugify.lowercase_after_regexp', ($data['slugify']['lowercase_after_regexp'] == '1' ? true : false));
        Arr::set($data, 'slugify.strip_tags', ($data['slugify']['strip_tags'] == '1' ? true : false));
        Arr::set($data, 'slugify.trim', ($data['slugify']['trim'] == '1' ? true : false));
        Arr::set($data, 'slugify.lowercase', ($data['slugify']['lowercase'] == '1' ? true : false));
        Arr::set($data, 'cache.lifetime', (int) $data['cache']['lifetime']);
        Arr::set($data, 'entries.media.upload_images_quality', (int) $data['entries']['media']['upload_images_quality']);
        Arr::set($data, 'entries.media.upload_images_width', (int) $data['entries']['media']['upload_images_width']);
        Arr::set($data, 'entries.media.upload_images_height', (int) $data['entries']['media']['upload_images_height']);

        if (Filesystem::write(PATH['config']['site'] . '/settings.json', JsonParser::encode(array_merge($this->registry->get('settings'), $data)))) {
            $this->flash->addMessage('success', __('admin_message_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_settings_was_not_saved'));
        }

        return $response->withRedirect($this->router->pathFor('admin.settings.index'));
    }

}
