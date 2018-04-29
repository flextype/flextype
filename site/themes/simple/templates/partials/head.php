<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http};
?>
<!doctype html>
<html lang="<?php echo Config::get('site.locale'); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo Config::get('site.description'); ?>">
    <meta name="author" content="">

	<?php Event::dispatch('onThemeMeta'); ?>

	<title><?php echo $page['title']; ?> | <?php echo Config::get('site.title'); ?></title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>

    <!-- Bootstrap core CSS -->
	<link href="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
	<link href="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/assets/css/theme.css" rel="stylesheet">
	<?php Event::dispatch('onThemeHeader'); ?>
  </head>
  <body>
  <?php Themes::template('partials/navigation')->display(); ?>
  <main role="main" class="container content">
