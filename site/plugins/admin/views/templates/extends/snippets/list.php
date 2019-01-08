<?php namespace Flextype ?>
<?php use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Text\Text, Form\Form} ?>
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
                <a href="<?= Http::getBaseUrl() ?>/admin/snippets/edit?snippet=<?= basename($snippet, '.php') ?>"><?= basename($snippet, '.php') ?></a>
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
                    <a class="dropdown-item js-snippets-info" href="javascript:;"  data-toggle="modal" data-target="#snippetsInfoModal" data-name="<?= basename($snippet, '.php') ?>"><?= __('admin_embeded_code') ?></a>
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


<!-- Modal -->
<div class="modal fade" id="snippetsInfoModal" tabindex="-1" role="dialog" aria-labelledby="snippetsInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="snippetsInfoModalLabel"><?= __('admin_embeded_code') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <?= Form::label('shortcode', __('admin_shortcode'), ['for' => 'shortcode']) ?>
          <div class="alert alert-dark clipboard" role="alert">
              <span id="snippet">[snippet name="<span class="js-snippets-snippet-placeholder"></span>"]</span>
              <button class="js-clipboard-btn btn" data-clipboard-target="#snippet">
                  <?= __('admin_copy') ?>
              </button>
          </div>
          <br>
          <?= Form::label('php_code', __('admin_php_code'), ['for' => 'php_code']) ?>
           <div id="php" class="alert alert-dark clipboard" role="alert">
               <span id="php">&lt;?= Snippets::get("<span class="js-snippets-php-placeholder"></span>") ?&gt;</span>
               <button class="js-clipboard-btn btn" data-clipboard-target="#php">
                    <?= __('admin_copy') ?>
               </button>
          </div>
      </div>
    </div>
  </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
