<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http, Registry\Registry, Assets\Assets, Text\Text};
?>
<!doctype html>
<html lang="<?php echo Registry::get('site.locale'); ?>">
  <head>
    <meta charset="<?php echo Text::lowercase(Registry::get('site.charset')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo (isset($page['description']) ? $page['description'] : Registry::get('site.description')); ?>">
    <meta name="keywords" content="<?php echo (isset($page['keywords']) ? $page['keywords'] : Registry::get('site.keywords')); ?>">
    <meta name="robots" content="<?php echo (isset($page['robots']) ? $page['robots'] : Registry::get('site.robots')); ?>">
    <meta name="generator" content="Powered by Flextype <?php echo Flextype::VERSION; ?>" />

	<?php Event::dispatch('onThemeMeta'); ?>

	<title><?php echo $page['title']; ?> | <?php echo Registry::get('site.title'); ?></title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>

    <?php Assets::add('css', Http::getBaseUrl() . '/site/themes/' . Registry::get('site.theme') . '/assets/dist/css/bootstrap.min.css', 'site', 1); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/themes/' . Registry::get('site.theme') . '/assets/dist/css/simple.min.css', 'site', 2); ?>
    <?php foreach (Assets::get('css', 'site') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
        <link href="<?php echo $assets['asset']; ?>" rel="stylesheet">
    <?php } } ?>

    <?php Event::dispatch('onThemeHeader'); ?>
  </head>
  <body>
  <?php Themes::view('partials/navigation')->display(); ?>
  <main role="main" class="container content">
