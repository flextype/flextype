<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class FieldsetsManager
{
    public static function getFieldsetsManager()
    {
        Registry::set('sidebar_menu_item', 'fieldsets');

        switch (Http::getUriSegment(2)) {
            case 'add':
                FieldsetsManager::addFieldsets();
            break;
            case 'delete':
                FieldsetsManager::deleteFieldsets();
            break;
            case 'rename':
                FieldsetsManager::renameFieldsets();
            break;
            case 'duplicate':
                FieldsetsManager::duplicateFieldsets();
            break;
            case 'edit':
                FieldsetsManager::editFieldsets();
            break;
            default:
                FieldsetsManager::listFieldsets();
            break;
        }
    }

    protected static function addFieldsets()
    {
        $create_fieldset = Http::post('create_fieldset');

        if (isset($create_fieldset)) {
            if (Token::check((Http::post('token')))) {

                $file = PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Text::safeString(Http::post('name'), '-', true) . '.yaml';

                if (!Filesystem::has($file)) {
                    // Create a fieldset!
                    if (Filesystem::write(
                            $file,
                            YamlParser::encode([
                                                'title' => Http::post('title'),
                                                'fields' => [
                                                    'title' => [
                                                        'title' => 'admin_title',
                                                        'type'  => 'text',
                                                        'size'  => 'col-12'
                                                    ]
                                                  ]
                                                ])
                    )) {
                        Notification::set('success', __('admin_message_fieldset_created'));
                        Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                    }
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/add')
            ->display();
    }

    protected static function renameFieldsets()
    {
        $rename_fieldset = Http::post('rename_fieldset');

        if (isset($rename_fieldset)) {
            if (Token::check((Http::post('token')))) {
                if (!Filesystem::has(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')) {
                    if (rename(
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name_current') . '.yaml',
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')
                    ) {
                        Notification::set('success', __('admin_message_fieldset_renamed'));
                        Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                    }
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/rename')
            ->assign('name_current', Http::get('fieldset'))
            ->display();
    }

    protected static function duplicateFieldsets()
    {
        if (Http::get('fieldset') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::copy(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml',
                                    PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '-duplicate-' . date("Ymd_His") . '.yaml');
                Notification::set('success', __('admin_message_fieldset_duplicated'));
                Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    protected static function deleteFieldsets()
    {
        if (Http::get('fieldset') != '') {
            if (Token::check((Http::get('token')))) {
                Filesystem::delete(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml');
                Notification::set('success', __('admin_message_fieldset_deleted'));
                Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    protected static function editFieldsets()
    {
        $action = Http::post('action');

        if (isset($action) && $action == 'save-form') {
            if (Token::check((Http::post('token')))) {

                // Save a fieldset!
                if (Filesystem::write(
                        PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml',
                        Http::post('fieldset')
                )) {
                    Notification::set('success', __('admin_message_fieldset_saved'));
                    Http::redirect(Http::getBaseUrl() . '/admin/fieldsets/edit?fieldset=' . Http::post('name'));
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/fieldsets/edit')
            ->assign('fieldset', Filesystem::read(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml'))
            ->display();
    }

    protected static function listFieldsets()
    {
        Themes::view('admin/views/templates/extends/fieldsets/list')
            ->assign('fieldsets_list', Themes::getFieldsets())
            ->display();
    }
}
