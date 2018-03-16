<?php Rawilum\Templates::display('partials/head'); ?>
<?php echo Rawilum\readingTime($page['content']); ?>
<?php echo Rawilum\readingTime($page['content'], [
    'minute'  => 'Minute!',
    'minutes' => 'Minutes!',
    'second'  => 'Second',
    'seconds' => 'Seconds']);
?>
<?php //cho Rawilum\redirect(); ?>
<?php Rawilum\Templates::display('partials/footer'); ?>
