<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<?= $entry['content'] ?>
<?php foreach (Entries::getEntries('blog', false, 'date', 'DESC', 0, 3) as $entry): ?>
    <a href="<?= $entry['url'] ?>">
        <h3><?= $entry['title'] ?></h3>
        <?= $entry['summary'] ?>
    </a>
<?php endforeach ?>
READ THE REST OF THE BLOG
<?php Themes::view('partials/footer')->display() ?>
