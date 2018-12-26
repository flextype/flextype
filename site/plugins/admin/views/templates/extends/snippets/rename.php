<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Form\Form;
use Flextype\Component\Html\Html;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                        'snippets' => [
                                        'link' => Http::getBaseUrl() . '/admin/snippets',
                                        'title' => __('admin_snippets'),
                                        'attributes' => ['class' => 'navbar-item']
                                    ],
                       'snippets_add' => [
                                        'link' => Http::getBaseUrl() . '/admin/snippets/rename?snippet=' . $name_current,
                                        'title' => __('admin_rename'),
                                        'attributes' => ['class' => 'navbar-item active']
                                      ]
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <?= Form::open() ?>
        <?= Form::hidden('token', Token::generate()) ?>
        <?= Form::hidden('name_current', $name_current) ?>
        <div class="form-group">
            <?= Form::label('name', __('admin_name'), ['for' => 'snippetName']) ?>
            <?= Form::input('name', $name_current, ['class' => 'form-control', 'id' => 'snippetName',  'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_snippet_error_title_empty_input')]) ?>
        </div>
        <?= Form::submit('rename_snippet', __('admin_save'), ['class' => 'btn btn-black btn-fill btn-wd']) ?>
        <?= Form::close() ?>
    </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
