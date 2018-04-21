<!doctype html>
<html lang="<?php echo Flextype\Config::get('site.locale'); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<?php Flextype\Events::dispatch('onAdminThemeMeta'); ?>

	<title>FLEXTYPE</title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>


    <style media="screen">
    .CodeMirror {
        height: auto!important;
        min-height: 10px!important;
    }
    </style>

    <!-- Bootstrap core CSS -->
	<link href="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
	<link href="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/css/theme.css" rel="stylesheet">
	<?php Flextype\Events::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body>
  <?php Flextype\View::factory('admin/views/partials/navigation')->display(); ?>
  <main role="main" class="container content">
