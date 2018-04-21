<?php Flextype\View::factory('admin/views/partials/head')->display(); ?>

<form method="post">
    <input id="slug" name="slug" type="hidden" value="<?php echo $page_slug; ?>">
    <textarea id="frontmatter" name="frontmatter" rows="0" cols="0"><?php echo $page_frontmatter; ?></textarea>
    <textarea id="editor" name="editor" rows="8" cols="80"><?php echo $page_content; ?></textarea>
    <button class="btn btn-lg btn-dark" name="save_page" type="submit"><?php echo Flextype\I18n::find('admin_save', 'admin', Flextype\Config::get('site.locale')); ?></button>
</form>

<?php Flextype\View::factory('admin/views/partials/footer')->display(); ?>
