<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http};
?>
<script src="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/node_modules/jquery/dist/jquery.slim.min.js"></script>
<script src="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Config::get('site.theme'); ?>/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<?php Event::dispatch('onThemeFooter'); ?>
