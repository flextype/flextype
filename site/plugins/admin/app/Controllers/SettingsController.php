<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;

class SettingsController extends Controller
{
    public function index($request, $response)
    {
        $entries = [];
        foreach ($this->entries->fetchAll('', 'date', 'DESC') as $entry) {
            $entries[$entry['slug']] = $entry['title'];
        }

        $themes = [];
        foreach (Filesystem::listContents(PATH['themes']) as $theme) {
            if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . $theme['dirname'] . '.json')) {
                $themes[$theme['dirname']] = $theme['dirname'];
            }
        }

        $available_locales = Filesystem::listContents(PATH['plugins'] . '/admin/languages/');
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

        return $this->view->render(
           $response,
           'plugins/admin/views/templates/system/settings/index.html',
           [
                                      'timezones' => Date::timezones(),
                                      'settings' => $this->registry->get('settings'),
                                      'cache_driver' => $cache_driver,
                                      'locales' => $locales,
                                      'entries' => $entries,
                                      'themes' => $themes,
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
                                                                                  ],
                                                                  'settings_clear_cache' => [
                                                                                      'type' => 'action',
                                                                                      'id' => 'clear-cache',
                                                                                      'link' => $this->router->pathFor('admin.settings.clear-cache'),
                                                                                      'title' => __('admin_clear_cache'),
                                                                                      'attributes' => ['class' => 'float-right btn']
                                                                              ]
                                                          ]
                                  ]
       );
    }

    public function update($request, $response)
    {
        $data = $request->getParsedBody();

        Arr::delete($data, 'csrf_name');
        Arr::delete($data, 'csrf_value');
        Arr::delete($data, 'action');

        Arr::set($data, 'errors.display', ($data['errors']['display'] == '1' ? true : false));
        Arr::set($data, 'cache.enabled', ($data['cache']['enabled'] == '1' ? true : false));
        Arr::set($data, 'cache.lifetime', (int) $data['cache']['lifetime']);
        Arr::set($data, 'entries.media.upload_images_quality', (int) $data['entries']['media']['upload_images_quality']);
        Arr::set($data, 'entries.media.upload_images_width', (int) $data['entries']['media']['upload_images_width']);
        Arr::set($data, 'entries.media.upload_images_height', (int) $data['entries']['media']['upload_images_height']);

        if (Filesystem::write(PATH['config']['site'] . '/settings.json', JsonParser::encode(array_merge($this->registry->get('settings'), $data)))) {
            $this->flash->addMessage('success', __('admin_message_settings_saved'));
        } else {
            $this->flash->addMessage('error', __('admin_message_settings_was_not_saved'));
        }

        return $response->withRedirect($this->container->get('router')->pathFor('admin.settings.index'));
    }

    public function clearCache($request, $response)
    {
        Cache::clear();
        $this->flash->addMessage('success', __('admin_message_cache_files_deleted'));
        return $response->withRedirect($this->container->get('router')->pathFor('admin.settings.index'));
    }
}
