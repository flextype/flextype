<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<h3><?= $entry['title'] ?></h3>
<?= $entry['content'] ?>
<?php Themes::view('partials/footer')->display() ?>
