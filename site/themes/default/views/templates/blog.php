<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<?php foreach (Entries::getEntries('blog') as $entry): ?>
    <a href="<?= $entry['url'] ?>">
        <h3><?= $entry['title'] ?></h3>
        <?= $entry['summary'] ?>
    </a>
<?php endforeach ?>
<?php Themes::view('partials/footer')->display() ?>
