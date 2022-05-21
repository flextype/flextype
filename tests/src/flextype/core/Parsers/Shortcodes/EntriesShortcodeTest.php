<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('(entries-fetch] shortcode', function () {
    $this->assertTrue(entries()->create('blog', ['title' => 'Blog', 'categories' => "@type[array] (entries fetch:\"blog,collection=true&filter[sort_by][key]=date&filter[sort_by][direction]=ASC\" /)"]));
    $this->assertTrue(entries()->create('blog/post-1', ['title' => 'Post 1']));
    $this->assertTrue(entries()->create('blog/post-2', ['title' => 'Post 2']));
    $this->assertTrue(entries()->create('blog/post-3', ['title' => 'Post 3']));
    $this->assertTrue(entries()->create('categories', ['title' => 'Categories']));
    $this->assertTrue(entries()->create('categories/cat', ['title' => 'Cat']));
    $this->assertTrue(entries()->create('categories/dog', ['title' => 'Dog']));

    expect(entries()->fetch('blog')->dot()->count())->toBe(44);

    $this->assertTrue(entries()->create('blog-2', ['title' => 'Blog', 'category-cat' => "(entries fetch:\"categories/cat\" field:\"title,Foo\" /)"]));
    expect(entries()->fetch('blog-2')['category-cat'])->toBe('Cat');

    $this->assertTrue(entries()->create('blog-3', ['title' => 'Blog', 'category-cat' => "(entries fetch:\"categories/cat\" field:\"title2,Foo\" /)"]));
    expect(entries()->fetch('blog-3')['category-cat'])->toBe('Foo');
});
