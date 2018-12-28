<?php namespace Flextype ?>
<?php use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Text\Text} ?>
<?php use function Flextype\Component\I18n\__; ?>
<?php Themes::view('admin/views/partials/head')->display() ?>
<?php Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'menus' => [
                                            'link' => Http::getBaseUrl() . '/admin/menus',
                                            'title' => __('admin_menus_heading'),
                                            'attributes' => ['class' => 'navbar-item active']
                                       ]
                        ])
    ->assign('buttons', [
                            'entries' => [
                                            'link' => Http::getBaseUrl() . '/admin/menus/add',
                                            'title' => __('admin_create_new_menu'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display()
?>
<?php Themes::view('admin/views/partials/content-start')->display() ?>

<?php if (count($menus_list) > 0): ?>
<table class="table no-margin">
    <thead>
        <tr>
            <th><?= __('admin_entries_name') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($menus_list as $name => $menu): ?>
        <tr>
            <td>
                <?= $menu['title'] ?>
            </td>
            <td class="text-right">
                <div class="btn-group">
                  <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/menus/edit?menu=<?= $name ?>"><?= __('admin_edit') ?></a>
                  <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/menus/rename?menu=<?= $name ?>"><?= __('admin_rename') ?></a>
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/menus/duplicate?menu=<?= $name ?>&token=<?= Token::generate() ?>"><?= __('admin_duplicate') ?></a>
                  </div>
                </div>
                <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/menus/delete?menu=<?= $name ?>&token=<?= Token::generate() ?>"><?= __('admin_delete') ?></a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>

<?php endif ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
