<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

		<?php Action::run('theme_meta'); ?>

		<link rel="shortcut icon" href="<?php echo Url::getBase(); ?>/favicon.ico">

		<title><?php echo Config::get('site.title'); ?> | <?php echo Pages::getCurrentPage()['title']; ?></title>

    <!-- Bootstrap core CSS -->
		<link href="<?php echo Url::getBase(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
		<link href="<?php echo Url::getBase(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/assets/css/theme.css" rel="stylesheet">
		<?php Action::run('theme_header'); ?>
  </head>
  <body>
  <?php Theme::getTemplate('partials/navigation'); ?>
