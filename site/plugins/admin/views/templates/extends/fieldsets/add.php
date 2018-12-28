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
                        'fieldsets' => [
                                        'link' => Http::getBaseUrl() . '/admin/fieldsets',
                                        'title' => __('admin_fieldsets'),
                                        'attributes' => ['class' => 'navbar-item']
                                    ],
                       'fieldsets_add' => [
                                        'link' => Http::getBaseUrl() . '/admin/fieldsets/add',
                                        'title' => __('admin_create_new_fieldset'),
                                        'attributes' => ['class' => 'navbar-item active']
                                      ]
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="row">
    <div class="col-md-6">
        <?= Form::open(); ?>
        <?= Form::hidden('token', Token::generate()); ?>
        <div class="form-group">
            <?= Form::label('title', __('admin_title'), ['for' => 'fieldsetTitle']) ?>
            <?= Form::input('title', '', ['class' => 'form-control', 'id' => 'fieldsetTitle', 'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_fieldsets_error_name_empty_input')]) ?>
        </div>
        <div class="form-group">
            <?= Form::label('name', __('admin_name'), ['for' => 'fieldsetName']) ?>
            <?= Form::input('name', '', ['class' => 'form-control', 'id' => 'fieldsetName', 'required', 'data-validation' => 'length required', 'data-validation-allowing' => '-_', 'data-validation-length' => 'min1', 'data-validation-error-msg' => __('admin_fieldsets_error_name_empty_input')]) ?>
        </div>
    </div>
</div>
<?= Form::submit('create_fieldset', __('admin_create'), ['class' => 'btn btn-black']) ?>
<?= Form::close() ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
