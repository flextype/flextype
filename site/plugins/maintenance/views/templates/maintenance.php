<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http, Registry\Registry};
?>
<!doctype html>
<html lang="<?php echo Registry::get('site.locale'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo Registry::get('plugins.maintenance.msg_description'); ?>">
    <meta name="author" content="">

    <?php Event::dispatch('onThemeMeta'); ?>

    <title><?php echo Registry::get('plugins.maintenance.msg_title'); ?> | <?php echo Registry::get('site.title'); ?></title>

    <?php Event::dispatch('onThemeHeader'); ?>

    <style>

        body {
            margin: 0;
        }

        html {

            font-family: sans-serif;

            -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;

            background: url(<?php echo Registry::get('plugins.maintenance.bg_img'); ?>) no-repeat center center fixed;

            -webkit-background-size: cover;
               -moz-background-size: cover;
                 -o-background-size: cover;
                    background-size: cover;
        }

        .promo-block {
            font-size: 42px;
            text-shadow: 1px 1px 1px rgba(0,0,0,.4);
            color: #fff;
            display: block;
            margin-top: 160px;
            padding: 50px;
            text-align: center;
        }

        h1 {
            font-size: 66px;
            padding: 0;
            margin: 0;
        }

        p {
            padding: 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="promo-block">
        <h1><?php echo Registry::get('plugins.maintenance.msg_title'); ?></h1>
        <p><?php echo Registry::get('plugins.maintenance.msg_description'); ?></p>
    </div>
</body>
</html>
