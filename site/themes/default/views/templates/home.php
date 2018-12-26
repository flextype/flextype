<?php namespace Flextype ?>
<?php Themes::view('partials/head')->display() ?>
<?= $entry['content'] ?>
<?php foreach (Entries::getEntries('blog', 'date', 'DESC', 0, 3) as $entry): ?>
    <a href="<?= $entry['url'] ?>" class="blog-post">
        <h3><?= $entry['title'] ?></h3>
        <p><?= $entry['summary'] ?></p>
        <div><?= $entry['date'] ?></div>
    </a>
<?php endforeach ?>
<a href="./blog" class="blog-read">Read the rest of the blog</a>
<?php Themes::view('partials/footer')->display() ?>
