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
                                                            'attributes' => ['class' => 'navbar-item active']
                                                        ],
                                    'edit_entry_source'           => [
                                                                'link'       => Http::getBaseUrl() . '/admin/entries/edit?entry=' . $entry_name . '&source=true',
                                                                'title'      => __('admin_entries_editor_source'),
                                                                'attributes' => ['class' => 'navbar-item']
                                                             ]
                            ])
        ->assign('entry', $entry)
        ->display();
    Themes::view('admin/views/partials/content-start')->display();
?>


<?= Form::open(null, ['enctype' => 'multipart/form-data', 'class' => 'form-inline form-upload']) ?>
<?= Form::hidden('token', Token::generate()) ?>
<?= Form::file('file') ?>
<?= Form::submit('upload_file', __('admin_entries_files_upload'), ['class' => '']) ?>
<?= Form::close() ?>

<br>

<div class="media-manager">
    <div class="row">
        <?php foreach($files as $file): ?>
            <div class="col-sm-2">
                <div class="item">
                    <a href="javascript:;"
                       <?php $file_ext = substr(strrchr($file, '.'), 1) ?>
                       <?php if(in_array($file_ext, ['jpeg', 'png', 'gif', 'jpg'])): ?>
                       style="background-image: url('<?= Http::getBaseUrl() . '/site/entries/' . Http::get('entry') . '/' . basename($file) ?>')"
                       <?php else: ?>
                       style="background: #000;"
                       <?php endif ?>
                       class="img-item js-entries-image-preview"
                       data-image-delete-url="<?= Http::getBaseUrl() ?>/admin/entries/edit?entry=<?= Http::get('entry') ?>&delete_file=<?= basename($file) ?>&media=true&token=<?= Token::generate() ?>"
                       data-image-url="<?= Http::getBaseUrl() . '/site/entries/' . Http::get('entry') . '/' . basename($file) ?>">
                       <i class="fas fa-eye"></i>
                       <?php if(!in_array($file_ext, ['jpeg', 'png', 'gif', 'jpg'])): ?>
                       <span class="file-ext"><?= $file_ext ?></span>
                       <?php endif ?>
                    </a>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>

<div class="modal animated fadeIn faster image-preview-modal" id="entriesImagePreview" tabindex="-1" role="dialog" aria-labelledby="entriesImagePreviewLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="entriesImagePreviewLabel"><?= __('admin_entries_image_preview') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body js-entry-image-preview-placeholder image-preview">
      </div>
      <div class="modal-footer">
          <input type="text" name="" class="form-control js-entry-image-url-placeholder" value="">
          <a href="#" class="js-entry-image-delete-url-placeholder btn btn-primary"><?= __('admin_entries_files_delete') ?></a>
      </div>
    </div>
  </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
