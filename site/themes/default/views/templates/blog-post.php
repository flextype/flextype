<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<h1><?= $entry['title'] ?></h1>
<div class="blog-post">
<?= $entry['content'] ?>
</div>
<?php Themes::view('partials/footer')->display() ?>
