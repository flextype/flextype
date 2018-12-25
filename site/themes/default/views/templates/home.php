<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<?= $entry['content'] ?>
<?php foreach (Entries::getEntries('blog') as $post): ?>
    <?= $post['title'] ?>
<?php endforeach ?>
<?php Themes::view('partials/footer')->display() ?>
