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

        // Create directory for snippets
        !Filesystem::has(PATH['snippets']) and Filesystem::createDir(PATH['snippets']);

        switch (Http::getUriSegment(2)) {
            case 'add':
                SnippetsManager::addSnippet();
            break;
            case 'delete':
                SnippetsManager::deleteSnippet();
            break;
            case 'rename':
                SnippetsManager::renameSnippet();
            break;
            case 'duplicate':
                SnippetsManager::duplicateSnippet();
            break;
            case 'edit':
                SnippetsManager::editSnippet();
            break;
            default:
                SnippetsManager::listSnippet();
            break;
        }
    }

    private static function editSnippet()
    {
        $action = Http::post('action');

        if (isset($action) && $action == 'save-form') {
            if (Token::check((Http::post('token')))) {

                // Save a snippet!
                if (Snippets::update(
                        Http::post('name'),
                        Http::post('snippet')
                )) {
                    Notification::set('success', __('admin_message_snippet_saved'));
                } else {
                    Notification::set('error', __('admin_message_snippet_was_not_saved'));
                }

                Http::redirect(Http::getBaseUrl() . '/admin/snippets/edit?snippet=' . Http::post('name'));
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/snippets/edit')
            ->assign('snippet', Filesystem::read(PATH['snippets'] . '/' . Http::get('snippet') . '.php'))
            ->display();
    }

    private static function listSnippet()
    {
        $snippets = [];

        foreach (Filesystem::listContents(PATH['snippets']) as $snippet) {
            if ($snippet['type'] == 'file' && $snippet['extension'] == 'php') {
                $snippets[$snippet['basename']] = $snippet['basename'];
            }
        }

        Themes::view('admin/views/templates/extends/snippets/list')
        ->assign('snippets_list', $snippets)
        ->display();
    }

    private static function duplicateSnippet()
    {
        if (Http::get('snippet') != '') {
            if (Token::check((Http::get('token')))) {
                if (Snippets::copy(Http::get('snippet'),
                                   Http::get('snippet') . '-duplicate-' . date("Ymd_His"))) {
                    Notification::set('success', __('admin_message_snippet_duplicated'));
                } else {
                    Notification::set('error', __('admin_message_snippet_was_not_duplicated'));
                }

                Http::redirect(Http::getBaseUrl() . '/admin/snippets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    private static function renameSnippet()
    {
        $rename_snippet = Http::post('rename_snippet');

        if (isset($rename_snippet)) {
            if (Token::check((Http::post('token')))) {
                if (!Snippets::has(Http::post('name'))) {
                    if (Snippets::rename(
                        Http::post('name_current'),
                        Http::post('name'))
                    ) {
                        Notification::set('success', __('admin_message_snippet_renamed'));
                    } else {
                        Notification::set('error', __('admin_message_snippet_was_not_renamed'));
                    }
                    Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/snippets/rename')
            ->assign('name_current', Http::get('snippet'))
            ->display();
    }

    private static function deleteSnippet()
    {
        if (Http::get('snippet') != '') {
            if (Token::check((Http::get('token')))) {

                if (Snippets::delete(Http::get('snippet'))) {
                    Notification::set('success', __('admin_message_snippet_deleted'));
                } else {
                    Notification::set('error', __('admin_message_snippet_was_not_deleted'));
                }

                Http::redirect(Http::getBaseUrl() . '/admin/snippets');
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }
    }

    private static function addSnippet()
    {
        $create_snippet = Http::post('create_snippet');

        if (isset($create_snippet)) {
            if (Token::check((Http::post('token')))) {

                $snippet_name = Text::safeString(Http::post('name'), '-', true);

                if (!Snippets::has($snippet_name)) {

                    // Create a snippet!
                    if (Snippets::create($snippet_name)) {
                        Notification::set('success', __('admin_message_snippet_created'));
                    } else {
                        Notification::set('error', __('admin_message_snippet_was_not_created'));
                    }

                    Http::redirect(Http::getBaseUrl() . '/admin/snippets');
                }
            } else {
                throw new \RuntimeException("Request was denied because it contained an invalid security token. Please refresh the page and try again.");
            }
        }

        Themes::view('admin/views/templates/extends/snippets/add')
            ->display();
    }
}
