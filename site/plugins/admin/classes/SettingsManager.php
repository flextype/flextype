<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Token\Token;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class SettingsManager
{
    public static function getSettingsManager()
    {
        Registry::set('sidebar_menu_item', 'settings');

        SettingsManager::clearCache();
        SettingsManager::saveSettings();

        Themes::view('admin/views/templates/system/settings/list')
                ->assign('settings', Registry::get('settings'))
                ->assign('cache_driver', SettingsManager::cacheDriverList())
                ->assign('locales', SettingsManager::localesList())
                ->assign('entries', SettingsManager::entriesList())
                ->assign('themes', SettingsManager::themesList())
                ->display();
    }

    private static function saveSettings()
    {
        if (Http::post('action') !== null && Http::post('action') == 'save-form' && Http::post('token') !== null) {
            if (Token::check((Http::post('token')))) {

                $settings = $_POST;

                Arr::delete($settings, 'token');
                Arr::delete($settings, 'action');
                Arr::set($settings, 'errors.display', (Http::post('errors.display') == '1' ? true : false));
                Arr::set($settings, 'cache.enabled', (Http::post('cache.enabled') == '1' ? true : false));
                Arr::set($settings, 'cache.lifetime', (int) Http::post('cache.lifetime'));
                Arr::set($settings, 'entries.media.upload_images_quality', (int) Http::post('entries.media.upload_images_quality'));
                Arr::set($settings, 'entries.media.upload_images_width', (int) Http::post('entries.media.upload_images_width'));
                Arr::set($settings, 'entries.media.upload_images_height', (int) Http::post('entries.media.upload_images_height'));

                if (Filesystem::write(PATH['config']['site'] . '/settings.yaml', YamlParser::encode(array_merge(Registry::get('settings'), $settings)))) {
                    Notification::set('success', __('admin_message_settings_saved'));
                    Http::redirect(Http::getBaseUrl() . '/admin/settings');
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    private static function clearCache()
    {
        // Clear cache
        if (Http::get('clear_cache') !== null && Http::get('clear_cache') == '1' && Http::get('token') !== null) {
            if (Token::check((Http::get('token')))) {
                Cache::clear();
                Notification::set('success', __('admin_message_cache_files_deleted'));
                Http::redirect(Http::getBaseUrl() . '/admin/settings');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    private static function localesList()
    {
        $available_locales = Filesystem::listContents(PATH['plugins'] . '/admin/languages/');
        $system_locales = Plugins::getLocales();

        $locales = [];

        foreach ($available_locales as $locale) {
            if ($locale['type'] == 'file' && $locale['extension'] == 'yaml') {
                $locales[$locale['basename']] = $system_locales[$locale['basename']]['nativeName'];
            }
        }

        return $locales;
    }

    private static function entriesList()
    {
        $entries = [];

        foreach (Entries::fetchAll('', 'date', 'DESC') as $entry) {
            $entries[$entry['slug']] = $entry['title'];
        }

        return $entries;
    }

    private static function themesList()
    {
        $themes = [];

        foreach (Filesystem::listContents(PATH['themes']) as $theme) {
            if ($theme['type'] == 'dir' && Filesystem::has($theme['path'] . '/' . $theme['dirname'] . '.yaml')) {
                $themes[$theme['dirname']] = $theme['dirname'];
            }
        }

        return $themes;
    }

    private static function cacheDriverList()
    {
        return ['auto' => 'Auto Detect',
                            'file' => 'File',
                            'apcu' => 'APCu',
                            'wincache' => 'WinCache',
                            'memcached' => 'Memcached',
                            'redis' => 'Redis',
                            'sqlite3' => 'SQLite3',
                            'zend' => 'Zend',
                            'array' => 'Array'];
    }
}
