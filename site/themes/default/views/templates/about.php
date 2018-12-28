<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<h1><?= $entry['title'] ?></h1>
<?= $entry['content'] ?>
<img src="<?= $entry['base_url'] ?>/<?= $entry['slug'] ?>/<?= $entry['image'] ?>">
<?php Themes::view('partials/footer')->display() ?>
