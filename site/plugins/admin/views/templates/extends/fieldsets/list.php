<?php namespace Flextype ?>
<?php use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Text\Text} ?>
<?php use function Flextype\Component\I18n\__; ?>
<?php Themes::view('admin/views/partials/head')->display() ?>
<?php Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'fieldsets' => [
                                            'link' => Http::getBaseUrl() . '/admin/fieldsets',
                                            'title' => __('admin_fieldsets'),
                                            'attributes' => ['class' => 'navbar-item active']
                                       ]
                        ])
    ->assign('buttons', [
                            'entries' => [
                                            'link' => Http::getBaseUrl() . '/admin/fieldsets/add',
                                            'title' => __('admin_create_new_fieldset'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display()
?>
<?php Themes::view('admin/views/partials/content-start')->display() ?>

<?php if (count($fieldsets_list) > 0): ?>
<table class="table no-margin">
    <thead>
        <tr>
            <th><?= __('admin_entries_name') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fieldsets_list as $name => $fieldset): ?>
        <tr>
            <td>
                <?= $fieldset ?>
            </td>
            <td class="text-right">
                <div class="btn-group">
                  <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/fieldsets/edit?fieldset=<?= $name ?>"><?= __('admin_edit') ?></a>
                  <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/fieldsets/rename?fieldset=<?= $name ?>"><?= __('admin_entries_rename') ?></a>
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/fieldsets/duplicate?fieldset=<?= $name ?>&token=<?= Token::generate() ?>"><?= __('admin_duplicate') ?></a>
                  </div>
                </div>
                <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/fieldsets/delete?fieldset=<?= $name ?>&token=<?= Token::generate() ?>"><?= __('admin_delete') ?></a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>

<?php endif ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
