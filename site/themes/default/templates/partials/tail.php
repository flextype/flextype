<?php namespace Flextype ?>
<?php use Flextype\Component\{Event\Event, Http\Http, Registry\Registry, Assets\Assets} ?>
<?php Assets::add('js', Http::getBaseUrl() . '/site/themes/' . Registry::get('settings.theme') . '/assets/dist/js/default.min.js', 'site', 1) ?>
<?php $assets_site = Assets::get('js', 'site') ?>
<?php if (count($assets_site) > 0): ?>
    <?php foreach ($assets_site as $assets_by_priorities): ?>
        <?php foreach ($assets_by_priorities as $assets): ?>
            <script src="<?= $assets['asset'] ?>"></script>
        <?php endforeach ?>
    <?php endforeach ?>
<?php endif ?>
<?php Event::dispatch('onThemeFooter') ?>
