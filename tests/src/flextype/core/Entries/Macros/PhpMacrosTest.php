<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('PhpMacros', function () {
    entries()->create('blog', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog-php-macros/entry.yaml')->get()));
    $this->assertTrue(entries()->create('blog/post-1', ['title' => 'Post 1']));
    $this->assertTrue(entries()->create('blog/post-2', ['title' => 'Post 2']));
    $this->assertTrue(entries()->create('blog/post-3', ['title' => 'Post 3']));
    $this->assertTrue(entries()->create('categories', ['title' => 'Categories']));
    $this->assertTrue(entries()->create('categories/cat', ['title' => 'Cat']));
    $this->assertTrue(entries()->create('categories/dog', ['title' => 'Dog']));

    expect(entries()->fetch('blog')->dot()->count())->toBe(44);
});
