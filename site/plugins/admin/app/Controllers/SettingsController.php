<?php

namespace Flextype;

use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Date\Date;
use Flextype\Component\Registry\Registry;
use function Flextype\Component\I18n\__;

class SettingsController extends Controller
{
   public function index($request, $response, $args)
   {

       $entries = [];
       foreach ($this->entries->fetchAll('', 'date', 'DESC') as $entry) {
           $entries[$entry['slug']] = $entry['title'];
       }

       $themes = [];
       foreach (Filesystem::listContents(PATH['themes']) as $theme) {
           if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . $theme['dirname'] . '.yaml')) {
               $themes[$theme['dirname']] = $theme['dirname'];
           }
       }

       $available_locales = Filesystem::listContents(PATH['plugins'] . '/admin/languages/');
       $system_locales = $this->plugins->getLocales();
       $locales = [];
       foreach ($available_locales as $locale) {
           if ($locale['type'] == 'file' && $locale['extension'] == 'yaml') {
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

       return $this->view->render($response,
                                  'plugins/admin/views/templates/system/settings/index.html', [
                                      'timezones' => Date::timezones(),
                                      'settings' => $this->registry->get('settings'),
                                      'cache_driver' => $cache_driver,
                                      'locales' => $locales,
                                      'entries' => $entries,
                                      'themes' => $themes,
                                      'links' => [
                                                              'settings' => [
                                                                                  'link' => '/admin/settings',
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
                                                                                      'link' => '/admin/settings?clear_cache=1&token=' . 'asd',
                                                                                      'title' => __('admin_clear_cache'),
                                                                                      'attributes' => ['class' => 'float-right btn']
                                                                              ]
                                                          ]
                                  ]);
   }
}
