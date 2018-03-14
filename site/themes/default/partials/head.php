<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<?php $this->rawilum['events']->dispatch('theme_meta'); ?>

	<link rel="shortcut icon" href="<?php echo Url::getBase(); ?>/favicon.ico">

	<title><?php echo $this->rawilum['config']->get('site.title'); ?> | <?php echo $page['title']; ?></title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>

    <!-- Bootstrap core CSS -->
	<link href="<?php echo Url::getBase(); ?>/site/themes/<?php echo $this->rawilum['config']->get('site.theme'); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
	<link href="<?php echo Url::getBase(); ?>/site/themes/<?php echo $this->rawilum['config']->get('site.theme'); ?>/assets/css/theme.css" rel="stylesheet">
	<?php $this->rawilum['events']->dispatch('theme_header'); ?>
  </head>
  <body>
  <?php $this->rawilum['themes']->getTemplate('partials/navigation'); ?>
  <main role="main" class="container content">
