<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http, Registry\Registry, Assets\Assets, Text\Text, Html\Html};
?>
<!doctype html>
<html lang="<?php echo Registry::get('settings.locale'); ?>">
  <head>
    <meta charset="<?php echo Text::lowercase(Registry::get('settings.charset')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo (isset($page['description']) ? Html::toText($page['description']) : Html::toText(Registry::get('settings.description'))); ?>">
    <meta name="keywords" content="<?php echo (isset($page['keywords']) ? $page['keywords'] : Registry::get('settings.keywords')); ?>">
    <meta name="robots" content="<?php echo (isset($page['robots']) ? $page['robots'] : Registry::get('settings.robots')); ?>">
    <meta name="generator" content="Powered by Flextype <?php echo Flextype::VERSION; ?>" />

	<?php Event::dispatch('onThemeMeta'); ?>

	<title><?php echo Html::toText($page['title']); ?> | <?php echo Html::toText(Registry::get('settings.title')); ?></title>

    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700' rel='stylesheet' type='text/css'>

    <?php Assets::add('css', Http::getBaseUrl() . '/site/themes/' . Registry::get('settings.theme') . '/assets/dist/css/bootstrap.min.css', 'site', 1); ?>
    <?php Assets::add('css', Http::getBaseUrl() . '/site/themes/' . Registry::get('settings.theme') . '/assets/dist/css/simple.min.css', 'site', 2); ?>
    <?php foreach (Assets::get('css', 'site') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
        <link href="<?php echo $assets['asset']; ?>" rel="stylesheet">
    <?php } } ?>

    <?php Event::dispatch('onThemeHeader'); ?>
  </head>
  <body>
  <?php Themes::view('partials/navigation')->display(); ?>
  <main role="main" class="container content">
