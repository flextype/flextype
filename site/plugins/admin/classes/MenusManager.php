<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class MenusManager
{
    public static function getMenusManager()
    {
        Registry::set('sidebar_menu_item', 'menus');

        // Create directory for menus
        !Filesystem::fileExists(PATH['menus']) and Filesystem::createDir(PATH['menus']);

        switch (Http::getUriSegment(2)) {
            case 'add':
                $create_menu = Http::post('create_menu');

                if (isset($create_menu)) {
                    if (Token::check((Http::post('token')))) {

                        $file = PATH['menus'] . '/' . Text::safeString(Http::post('name'), '-', true) . '.yaml';

                        if (!Filesystem::fileExists($file)) {
                            // Create a menu!
                            if (Filesystem::setFileContent(
                                  $file,
                                  YamlParser::encode(['title' => Http::post('title')])
                            )) {
                                Notification::set('success', __('admin_message_menu_created'));
                                Http::redirect(Http::getBaseUrl() . '/admin/menus');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/menus/add')
                    ->display();
            break;
            case 'delete':
                if (Http::get('menu') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::deleteFile(PATH['menus'] . '/' . Http::get('menu') . '.yaml');
                        Notification::set('success', __('admin_message_menu_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/menus');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            case 'rename':
                $rename_menu = Http::post('rename_menu');

                if (isset($rename_menu)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::fileExists(PATH['menus'] . '/' . Http::post('name') . '.yaml')) {
                            if (rename(
                                PATH['menus'] . '/' . Http::post('name_current') . '.yaml',
                                PATH['menus'] . '/' . Http::post('name') . '.yaml')
                            ) {
                                Notification::set('success', __('admin_message_menu_renamed'));
                                Http::redirect(Http::getBaseUrl() . '/admin/menus');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/menus/rename')
                    ->assign('name_current', Http::get('menu'))
                    ->display();
            break;
            case 'duplicate':
                if (Http::get('menu') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::copy(PATH['menus'] . '/' . Http::get('menu') . '.yaml',
                                         PATH['menus'] . '/' . Http::get('menu') . '-duplicate-' . date("Ymd_His") . '.yaml');
                        Notification::set('success', __('admin_message_menu_duplicated'));
                        Http::redirect(Http::getBaseUrl().'/admin/menus');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }
            break;
            case 'edit':
                $action = Http::post('action');

                if (isset($action) && $action == 'save-form') {
                    if (Token::check((Http::post('token')))) {

                        // Save a menu!
                        if (Filesystem::setFileContent(
                              PATH['menus'] . '/' . Http::post('name') . '.yaml',
                              Http::post('menu')
                        )) {
                            Notification::set('success', __('admin_message_menu_saved'));
                            Http::redirect(Http::getBaseUrl() . '/admin/menus/edit?menu=' . Http::post('name'));
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the entry and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/menus/edit')
                    ->assign('menu', Filesystem::getFileContent(PATH['menus'] . '/' . Http::get('menu') . '.yaml'))
                    ->display();
            break;
            default:
                $menus = [];
                $menus_list = [];

                $menus = Filesystem::getFilesList(PATH['menus'], 'yaml');

                if (count($menus) > 0) {
                     foreach ($menus as $menu) {
                         $menus_list[basename($menu, '.yaml')] = YamlParser::decode(Filesystem::getFileContent($menu));
                     }
                 }

                Themes::view('admin/views/templates/extends/menus/list')
                ->assign('menus_list', $menus_list)
                ->display();
            break;
        }
    }
}
