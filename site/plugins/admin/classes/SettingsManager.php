<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Number\Number;
use Flextype\Component\I18n\I18n;
use Flextype\Component\Http\Http;
use Flextype\Component\Event\Event;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Session\Session;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Form\Form;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;
use Gajus\Dindent\Indenter;

class SettingsManager
{
    public static function getSettingsManager()
    {
        Registry::set('sidebar_menu_item', 'settings');

        // Clear cache
        if (Http::get('clear_cache')) {
            if (Token::check((Http::get('token')))) {
                Cache::clear();
                Notification::set('success', __('admin_message_cache_files_deleted'));
                Http::redirect(Http::getBaseUrl().'/admin/settings');
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
            }
        }

        $action = Http::post('action');

        if (isset($action) && $action == 'save-form') {
            if (Token::check((Http::post('token')))) {

                $settings = $_POST;

                Arr::delete($settings, 'token');
                Arr::delete($settings, 'action');
                Arr::set($settings, 'errors.display', (Http::post('errors.display') == '1' ? true : false));
                Arr::set($settings, 'cache.enabled', (Http::post('cache.enabled') == '1' ? true : false));
                Arr::set($settings, 'cache.lifetime', (int) Http::post('cache.lifetime'));

                if (Filesystem::setFileContent(PATH['config']['site'] . '/settings.yaml', YamlParser::encode(array_merge(Registry::get('settings'), $settings)))) {
                    Notification::set('success', __('admin_message_settings_saved'));
                    Http::redirect(Http::getBaseUrl().'/admin/settings');
                }
            } else {
                die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
            }
        }

        $available_locales = Filesystem::getFilesList(PATH['plugins'] . '/admin/languages/', 'yaml');
        $system_locales = Plugins::getLocales();

        $locales = [];

        foreach ($available_locales as $locale) {
            $locales[basename($locale, '.yaml')] = $system_locales[basename($locale, '.yaml')];
        }

        Themes::view('admin/views/templates/system/settings/list')
                ->assign('settings', Registry::get('settings'))
                ->assign('locales', $locales)
                ->display();
    }
}
