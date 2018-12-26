<?php
namespace Flextype;
use Flextype\Component\{Registry\Registry, Html\Html, Form\Form, Http\Http, Token\Token};
use function Flextype\Component\I18n\__;
?>

<?php
    Themes::view('admin/views/partials/head')->display();
    Themes::view('admin/views/partials/navbar')
        ->assign('links', [
                                'edit_entry'           => [
                                                            'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name,
                                                            'title'      => __('admin_entries_editor'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                         ],
                                'edit_entry_media'     => [
                                                            'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name . '&media=true',
                                                            'title'      => __('admin_entries_edit_media'),
                                                            'attributes' => ['class' => 'navbar-item']
                                                        ],
                                    'edit_entry_source'           => [
                                                                'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name . '&source=true',
                                                                'title'      => __('admin_entries_editor_source'),
                                                                'attributes' => ['class' => 'navbar-item active']
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
?>

<?= Form::open(null, ['id' => 'form']) ?>
<?= Form::hidden('token', Token::generate()) ?>
<?= Form::hidden('action', 'save-form') ?>
<?= Form::hidden('entry_name', $entry_name) ?>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <?= Form::textarea('entry_content', $entry_content, ['class' => 'form-control', 'style' => 'min-height:500px;', 'id' => 'codeMirrorEditor']) ?>
        </div>
    </div>
</div>
<?= Form::close() ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
