<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http, Registry\Registry};
?>
<script src="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Registry::get('site.theme'); ?>/node_modules/jquery/dist/jquery.slim.min.js"></script>
<script src="<?php echo Http::getBaseUrl(); ?>/site/themes/<?php echo Registry::get('site.theme'); ?>/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<?php Event::dispatch('onThemeFooter'); ?>
