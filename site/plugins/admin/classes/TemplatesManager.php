<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class TemplatesManager
{
    public static function getTemplatesManager()
    {
        Registry::set('sidebar_menu_item', 'templates');

        switch (Http::getUriSegment(2)) {
            case 'add':
                $create_template = Http::post('create_template');

                if (isset($create_template)) {
                    if (Token::check((Http::post('token')))) {

                        $type = (Http::post('type') && Http::post('type') == 'partial') ? 'partial' : 'template';

                        $file = PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Text::safeString(Http::post('name'), '-', true) . '.php';

                        if (!Filesystem::fileExists($file)) {
                            // Create a template!
                            if (Filesystem::setFileContent(
                                  $file,
                                  ""
                            )) {
                                Notification::set('success', __('admin_message_template_created'));
                                Http::redirect(Http::getBaseUrl() . '/admin/templates');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/templates/add')
                    ->display();
            break;
            case 'delete':
                if (Http::get('template') != '') {
                    if (Token::check((Http::get('token')))) {
                        $type = (Http::get('type') && Http::get('type') == 'partial') ? 'partial' : 'template';
                        Filesystem::deleteFile(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::get('template') . '.php');
                        Notification::set('success', __('admin_message_template_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/templates');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'rename':
                $rename_template = Http::post('rename_template');

                if (isset($rename_template)) {
                    if (Token::check((Http::post('token')))) {
                        $type = (Http::post('type') && Http::post('type') == 'partial') ? 'partial' : 'template';
                        $type_current = (Http::post('type_current') && Http::post('type_current') == 'partial') ? 'partial' : 'template';
                        if (!Filesystem::fileExists(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::post('name') . '.php')) {
                            if (rename(
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type_current . 's' . '/' . Http::post('name_current') . '.php',
                                PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::post('name') . '.php')
                            ) {
                                Notification::set('success', __('admin_message_template_renamed'));
                                Http::redirect(Http::getBaseUrl() . '/admin/templates');
                            }
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                Themes::view('admin/views/templates/extends/templates/rename')
                    ->assign('name_current', Http::get('template'))
                    ->assign('type', ((Http::get('type') && Http::get('type') == 'partial') ? 'partial' : 'template'))
                    ->display();
            break;
            case 'duplicate':
                if (Http::get('template') != '') {
                    if (Token::check((Http::get('token')))) {
                        $type = (Http::get('type') && Http::get('type') == 'partial') ? 'partial' : 'template';
                        Filesystem::copy(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::get('template') . '.php',
                                         PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::get('template') . '-duplicate-' . date("Ymd_His") . '.php');
                        Notification::set('success', __('admin_message_template_duplicated'));
                        Http::redirect(Http::getBaseUrl().'/admin/templates');
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }
            break;
            case 'edit':
                $action = Http::post('action');

                if (isset($action) && $action == 'save-form') {
                    if (Token::check((Http::post('token')))) {

                        $type = (Http::post('type') && Http::post('type') == 'partial') ? 'partial' : 'template';

                        // Save a template!
                        if (Filesystem::setFileContent(
                              PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . 's' . '/' . Http::post('name') . '.php',
                              Http::post('template')
                        )) {
                            Notification::set('success', __('admin_message_template_saved'));
                            Http::redirect(Http::getBaseUrl() . '/admin/templates/edit?template=' . Http::post('name') . '&type=' . $type);
                        }
                    } else {
                        die('Request was denied because it contained an invalid security token. Please refresh the page and try again.');
                    }
                }

                $type = (Http::get('type') && Http::get('type') == 'partial') ? 'partials' : 'templates';

                Themes::view('admin/views/templates/extends/templates/edit')
                    ->assign('template', Filesystem::getFileContent(PATH['themes'] . '/' . Registry::get('settings.theme') . '/views/' . $type . '/' . Http::get('template') . '.php'))
                    ->assign('type', ((Http::get('type') && Http::get('type') == 'partial') ? 'partial' : 'template'))
                    ->display();
            break;
            default:

                Themes::view('admin/views/templates/extends/templates/list')
                ->assign('templates_list', Themes::getTemplates())
                ->assign('partials_list', Themes::getPartials())
                ->display();
            break;
        }
    }
}
