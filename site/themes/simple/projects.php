<?php Flextype\Templates::display('partials/head'); ?>
<?php echo $page['content']; ?>
<?php $projects = Flextype\Pages::getPages('projects', false , 'date'); ?>
<?php foreach ($projects as $project) { ?>
    <a href="<?php echo $project['url']; ?>"><?php echo $project['title']; ?></a>
<?php } ?>
<?php Flextype\Templates::display('partials/footer'); ?>
