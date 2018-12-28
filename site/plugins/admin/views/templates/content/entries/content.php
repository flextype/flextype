<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Html\Html;
use Flextype\Component\Form\Form;
use Flextype\Component\Http\Http;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                            'edit_entry'           => [
                                                        'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name,
                                                        'title'      => __('admin_entries_editor'),
                                                        'attributes' => ['class' => 'navbar-item active']
                                                     ],
                            'edit_entry_media'     => [
                                                        'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name . '&media=true',
                                                        'title'      => __('admin_entries_edit_media'),
                                                        'attributes' => ['class' => 'navbar-item']
                                                    ],
                                'edit_entry_source'           => [
                                                            'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name . '&source=true',
                                                            'title'      => __('admin_entries_editor_source'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ]
                        ])
    ->assign('buttons', [
                            'save_entry' => [
                                                'link'       => 'javascript:;',
                                                'title'      => __('admin_save'),
                                                'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                            ],
                            'view_entry' => [
                                                'link'       => Http::getBaseUrl() . '/' . $entry_name,
                                                'title'      => __('admin_preview'),
                                                'attributes' => ['class' => 'float-right btn', 'target' => '_blank']
                                            ]
                        ])
    ->assign('entry', $entry)
    ->display();
Themes::view('admin/views/partials/content-start')->display();

EntriesManager::displayEntryForm($fieldset['fields'], $entry, $entry['content']);

Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
