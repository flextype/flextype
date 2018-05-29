<?php
    namespace Flextype;
    use Flextype\Component\{Event\Event, Http\Http, Registry\Registry, Assets\Assets};
?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/themes/' . Registry::get('site.theme') . '/assets/dist/js/simple.min.js', 'site', 1); ?>
<?php foreach (Assets::get('js', 'site') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
    <script src="<?php echo $assets['asset']; ?>"></script>
<?php } } ?>
<?php Event::dispatch('onThemeFooter'); ?>
