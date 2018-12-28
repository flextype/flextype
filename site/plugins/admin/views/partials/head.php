<?php
namespace Flextype;

use Flextype\Component\Http\Http;
use Flextype\Component\Registry\Registry;
use Flextype\Component\Event\Event;
use Flextype\Component\Assets\Assets;
use function Flextype\Component\I18n\__;

?>
<!doctype html>
<html lang="<?php echo Registry::get('settings.locale'); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

	<?php Event::dispatch('onAdminThemeMeta'); ?>

	<title>FLEXTYPE</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <?php Assets::add('css', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/css/build.min.css', 'admin', 8); ?>

    <?php foreach (Assets::get('css', 'admin') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
        <link href="<?php echo $assets['asset']; ?>" rel="stylesheet">
    <?php } } ?>

    <style media="screen">
        .content-full-size .main-panel .navbar-fixed+.content {
            padding-top: 61px;
        }

        .content-full-size .main-panel .content {
            padding: 0;
        }

        .content-full-size .container-fluid {
            padding: 0;
        }
    </style>
	<?php Event::dispatch('onAdminThemeHeader'); ?>
  </head>
  <body <?php if(Http::get('preview') && Http::get('preview') == 'true') { ?> class="content-full-size" <?php } ?>>
      <div class="wrapper">
        <?php UsersManager::isLoggedIn() and Themes::view('admin/views/partials/sidebar')->display(); ?>
        <div class="main-panel <?php if (isset($main_panel_class)) { echo $main_panel_class; }?>">
