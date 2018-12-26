<?php namespace Flextype ?>
<?php use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Text\Text} ?>
<?php use function Flextype\Component\I18n\__; ?>
<?php Themes::view('admin/views/partials/head')->display() ?>
<?php Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'snippets' => [
                                            'link' => Http::getBaseUrl() . '/admin/snippets',
                                            'title' => __('admin_snippets'),
                                            'attributes' => ['class' => 'navbar-item active']
                                       ]
                        ])
    ->assign('buttons', [
                            'entries' => [
                                            'link' => Http::getBaseUrl() . '/admin/snippets/add',
                                            'title' => __('admin_create_new_snippet'),
                                            'attributes' => ['class' => 'float-right btn']
                                       ]
                        ])
    ->display()
?>
<?php Themes::view('admin/views/partials/content-start')->display() ?>

<?php if (count($snippets_list) > 0): ?>
<table class="table no-margin">
    <thead>
        <tr>
            <th><?= __('admin_entries_name') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($snippets_list as $snippet): ?>
        <tr>
            <td>
                <?= basename($snippet, '.php') ?>
            </td>
            <td class="text-right">
                <div class="btn-group">
                  <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/snippets/edit?snippet=<?= basename($snippet, '.php') ?>"><?= __('admin_entries_edit') ?></a>
                  <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/snippets/rename?snippet=<?= basename($snippet, '.php') ?>"><?= __('admin_entries_rename') ?></a>
                    <a class="dropdown-item" href="<?= Http::getBaseUrl() ?>/admin/snippets/duplicate?snippet=<?= basename($snippet, '.php') ?>&token=<?= Token::generate() ?>"><?= __('admin_duplicate') ?></a>
                  </div>
                </div>
                <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/snippets/delete?snippet=<?= basename($snippet, '.php') ?>&token=<?= Token::generate() ?>"><?= __('admin_entries_delete') ?></a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php else: ?>

<?php endif ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
