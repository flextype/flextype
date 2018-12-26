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
                $create_fieldset = Http::post('create_fieldset');

                if (isset($create_fieldset)) {
                    if (Token::check((Http::post('token')))) {

                        $file = PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Text::safeString(Http::post('name'), '-', true) . '.yaml';

                        if (!Filesystem::fileExists($file)) {
                            // Create a fieldset!
                            if (Filesystem::setFileContent(
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
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/fieldsets/add')
                    ->display();
            break;
            case 'delete':
                if (Http::get('fieldset') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteFile(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml');
                        Notification::set('success', __('admin_message_fieldset_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            case 'rename':
                $rename_fieldset = Http::post('rename_fieldset');

                if (isset($rename_fieldset)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')) {
                            if (rename(
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name_current') . '.yaml',
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml')
                            ) {
                                Notification::set('success', __('admin_message_fieldset_renamed'));
                                Http::redirect(Http::getBaseUrl() . '/admin/fieldsets');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/fieldsets/rename')
                    ->assign('name_current', Http::get('fieldset'))
                    ->display();
            break;
            case 'duplicate':
                if (Http::get('fieldset') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::copy(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml',
                                         PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '-duplicate-' . date("Ymd_His") . '.yaml');
                        Notification::set('success', __('admin_message_fieldset_duplicated'));
                        Http::redirect(Http::getBaseUrl().'/admin/fieldsets');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            case 'edit':
                $action = Http::post('action');

                if (isset($action) && $action == 'save-form') {
                    if (Token::check((Http::post('token')))) {

                        // Save a fieldset!
                        if (Filesystem::setFileContent(
                              PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::post('name') . '.yaml',
                              Http::post('fieldset')
                        )) {
                            Notification::set('success', __('admin_message_fieldset_saved'));
                            Http::redirect(Http::getBaseUrl() . '/admin/fieldsets/edit?fieldset='.Http::post('name'));
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/fieldsets/edit')
                    ->assign('fieldset', Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('settings.theme') . '/fieldsets/' . Http::get('fieldset') . '.yaml'))
                    ->display();
            break;
            default:
                $fieldsets_list = Themes::getFieldsets();

                Themes::view('admin/views/templates/extends/fieldsets/list')
                ->assign('fieldsets_list', $fieldsets_list)
                ->display();
            break;
        }
    }
}
