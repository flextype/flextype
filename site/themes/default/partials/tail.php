<script src="<?php echo Url::getBase(); ?>/site/themes/<?php echo $this->rawilum['config']->get('site.theme'); ?>/node_modules/jquery/dist/jquery.slim.min.js"></script>
<script src="<?php echo Url::getBase(); ?>/site/themes/<?php echo $this->rawilum['config']->get('site.theme'); ?>/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<?php $this->rawilum['events']->dispatch('theme_footer'); ?>
