<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('entries shortcode', function () {
    $this->assertTrue(entries()->create('blog', ['title' => 'Blog', 'categories' => "@type[array] (entries fetch id:'blog' options:'collection=true&filter[sort_by][key]=date&filter[sort_by][direction]=ASC' /)"]));
    $this->assertTrue(entries()->create('blog/post-1', ['title' => 'Post 1']));
    $this->assertTrue(entries()->create('blog/post-2', ['title' => 'Post 2']));
    $this->assertTrue(entries()->create('blog/post-3', ['title' => 'Post 3']));
    $this->assertTrue(entries()->create('categories', ['title' => 'Categories']));
    $this->assertTrue(entries()->create('categories/cat', ['title' => 'Cat']));
    $this->assertTrue(entries()->create('categories/dog', ['title' => 'Dog']));

    expect(entries()->fetch('blog')->dot()->count())->toBe(44);

    $this->assertTrue(entries()->create('blog-2', ['title' => 'Blog', 'category-cat' => "(entries fetch id:'categories/cat' field:'title' default:'foo' /)"]));
    expect(entries()->fetch('blog-2')['category-cat'])->toBe('Cat');

    $this->assertTrue(entries()->create('blog-3', ['title' => 'Blog', 'category-cat' => "(entries fetch id:'categories/cat' field:'title2' default:'Foo' /)"]));
    expect(entries()->fetch('blog-3')['category-cat'])->toBe('Foo');

    $this->assertTrue(entries()->create('shop', ['vars' => ['id' => 'shop', 'options' => 'collection=true'], 'title' => 'Shop', 'products' => "@type[array] (entries fetch id:'(var:id)' options:'(var:options)' /)"]));
    $this->assertTrue(entries()->create('shop/product-1', ['title' => 'Product 1']));
    expect(count(entries()->fetch('shop')['products']))->toBe(1);
});

test('entries shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled', false);
    expect(entries()->create('foo', ['test' => ""]))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled', true);
});