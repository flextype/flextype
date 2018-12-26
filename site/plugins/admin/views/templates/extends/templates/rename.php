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
                        'templates' => [
                                        'link' => Http::getBaseUrl() . '/admin/templates',
                                        'title' => __('admin_templates'),
                                        'attributes' => ['class' => 'navbar-item']
                                    ],
                       'templates_add' => [
                                        'link' => Http::getBaseUrl() . '/admin/templates/rename?template=' . $name_current,
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
        <?= Form::hidden('type_current', $type) ?>
        <div class="form-group">
            <?= Form::label('name', __('admin_name'), ['for' => 'templateName']) ?>
            <?= Form::input('name', $name_current, ['class' => 'form-control', 'id' => 'templateName',  'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_template_error_title_empty_input')]) ?>
        </div>
        <div class="form-group">
            <?= Form::label('type', __('admin_type'), ['for' => 'templateType']) ?>
            <?= Form::select('type', ['template' => __('admin_template'), 'partial' => __('admin_partial')], $type, ['class' => 'form-control', 'id' => 'templateType']) ?>
        </div>
        <?= Form::submit('rename_template', __('admin_save'), ['class' => 'btn btn-black btn-fill btn-wd']) ?>
        <?= Form::close() ?>
    </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
