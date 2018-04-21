<?php Flextype\View::factory('admin/views/partials/head')->display(); ?>

<h2 class="page-heading">
    Create New Page
</h2>

<form method="post">
    <div class="row">
      <div class="col-4">
        <div class="form-group">
          <label for="formGroupPageTitleInput">Page title</label>
          <input type="text" name="title" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
        <div class="form-group">
          <label for="formGroupPageTitleInput">Page slug (url)</label>
          <input type="text" name="slug" class="form-control" id="formGroupPageTitleInput" placeholder="">
        </div>
      </div>
    </div>
    <br>
    <button class="btn btn-lg btn-dark" name="create_page" type="submit"><?php echo Flextype\I18n::find('admin_save', 'admin', Flextype\Config::get('site.locale')); ?></button>
</form>

<?php Flextype\View::factory('admin/views/partials/footer')->display(); ?>
