<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Token\Token;
use Flextype\Component\Text\Text;
use Flextype\Component\Notification\Notification;
use function Flextype\Component\I18n\__;

class SnippetsManager
{
    public static function getSnippetsManager()
    {
        Registry::set('sidebar_menu_item', 'snippets');

        // Create directory for logs
        !Filesystem::has(PATH['snippets']) and Filesystem::createDir(PATH['snippets']);

        switch (Http::getUriSegment(2)) {
            case 'add':
                $create_snippet = Http::post('create_snippet');

                if (isset($create_snippet)) {
                    if (Token::check((Http::post('token')))) {

                        $file = PATH['snippets'] . '/' . Text::safeString(Http::post('name'), '-', true) . '.php';

                        if (!Filesystem::has($file)) {
                            // Create a snippet!
                            if (Filesystem::write(
                                    $file,
                                    ""
                            )) {
                                Notification::set('success', __('admin_message_snippet_created'));
                                Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                            }
                        }
                    } else {
                        throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
                    }
                }

                Themes::view('admin/views/templates/extends/snippets/add')
                    ->display();
            break;
            case 'delete':
                if (Http::get('snippet') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::delete(PATH['snippets'] . '/' . Http::get('snippet') . '.php');
                        Notification::set('success', __('admin_message_snippet_deleted'));
                        Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                    } else {
                        throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
                    }
                }
            break;
            case 'rename':
                $rename_snippet = Http::post('rename_snippet');

                if (isset($rename_snippet)) {
                    if (Token::check((Http::post('token')))) {
                        if (!Filesystem::has(PATH['snippets'] . '/' . Http::post('name') . '.php')) {
                            if (rename(
                                PATH['snippets'] . '/' . Http::post('name_current') . '.php',
                                PATH['snippets'] . '/' . Http::post('name') . '.php')
                            ) {
                                Notification::set('success', __('admin_message_snippet_renamed'));
                                Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                            }
                        }
                    } else {
                        throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
                    }
                }

                Themes::view('admin/views/templates/extends/snippets/rename')
                    ->assign('name_current', Http::get('snippet'))
                    ->display();
            break;
            case 'duplicate':
                if (Http::get('snippet') != '') {
                    if (Token::check((Http::get('token')))) {
                        Filesystem::copy(PATH['snippets'] . '/' . Http::get('snippet') . '.php',
                                            PATH['snippets'] . '/' . Http::get('snippet') . '-duplicate-' . date("Ymd_His") . '.php');
                        Notification::set('success', __('admin_message_snippet_duplicated'));
                        Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                    } else {
                        throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
                    }
                }
            break;
            case 'edit':
                $action = Http::post('action');

                if (isset($action) && $action == 'save-form') {
                    if (Token::check((Http::post('token')))) {

                        // Save a snippet!
                        if (Filesystem::write(
                                PATH['snippets'] . '/' . Http::post('name') . '.php',
                                Http::post('snippet')
                        )) {
                            Notification::set('success', __('admin_message_snippet_saved'));
                            Http::redirect(Http::getBaseUrl() . '/admin/snippets/edit?snippet=' . Http::post('name'));
                        }
                    } else {
                        throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
                    }
                }

                Themes::view('admin/views/templates/extends/snippets/edit')
                    ->assign('snippet', Filesystem::read(PATH['snippets'] . '/' . Http::get('snippet') . '.php'))
                    ->display();
            break;
            default:
                $snippets = [];

                foreach (Filesystem::listContents(PATH['snippets']) as $snippet) {
                    if ($snippet['type'] == 'file' && $snippet['extension'] == 'php') {
                        $snippets[$snippet['basename']] = $snippet['basename'];
                    }
                }

                Themes::view('admin/views/templates/extends/snippets/list')
                ->assign('snippets_list', $snippets)
                ->display();
            break;
        }
    }
}
