<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('entries field for blog', function () {
    entries()->create('blog', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/blog.yaml')->get()));
    entries()->create('blog/post-1', serializers()->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-1/post.md')->get()));
    entries()->create('blog/post-2', serializers()->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/blog/post-2/post.md')->get()));

    $blog = entries()->fetch('blog');

    $this->assertEquals(11, $blog->count());
});

test('EntriesField for catalog', function () {

    // Create catalog
    entries()->create('catalog', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/catalog/entry.yaml')->get()));
    entries()->create('catalog/bikes', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/catalog/bikes/entry.yaml')->get()));
    entries()->create('catalog/bikes/gt', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/catalog/bikes/gt/entry.yaml')->get()));
    entries()->create('catalog/bikes/norco', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/catalog/bikes/norco/entry.yaml')->get()));
    entries()->create('catalog/bikes/foo', ['title' => 'foo']);
    entries()->create('catalog/bikes/foo/bar', ['title' => 'bar']);

    // Create discounts
    entries()->create('discounts', ['title' => 'Discounts']);
    entries()->create('discounts/30-off', ['title' => '30% off', 'category' => 'bikes']);
    entries()->create('discounts/50-off', ['title' => '50% off', 'category' => 'bikes']);

    // Create banner
    entries()->create('banner', ['title' => 'Banner']);

    $catalogSingle = entries()->fetch('catalog');

    $this->assertEquals(15, $catalogSingle->count());
    $this->assertEquals('Catalog', $catalogSingle['title']);
    $this->assertEquals('catalog', $catalogSingle['id']);
    $this->assertEquals(1, $catalogSingle['bikes']->count());
    $this->assertTrue(isset($catalogSingle['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingle['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingle['discounts']->count());
    $this->assertTrue(isset($catalogSingle['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingle['discounts']['discounts/30-off']['title']);

    $catalogCollection = entries()->fetch('catalog', ['collection' => true]);
    $this->assertEquals(1, $catalogCollection->count());
    $this->assertEquals('Bikes', $catalogCollection['catalog/bikes']['title']);
    $this->assertEquals('catalog/bikes', $catalogCollection['catalog/bikes']['id']);

    $catalogLongCollecion = entries()->fetch('catalog', ['collection' => true, 'find' => ['depth' => ['>0', '<4']]]);
    $this->assertEquals(5, $catalogLongCollecion->count());

    $banner = entries()->fetch('banner');
    $this->assertEquals('Banner', $banner['title']);
    $this->assertEquals('banner', $banner['id']);
});

test('EntriesField for albmus', function () {
    entries()->create('root', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/root/entry.yaml')->get()));

    entries()->create('albums', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/root/albums/entry.yaml')->get()));
    entries()->create('albums/category-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/root/albums/category-1/entry.yaml')->get()));
    entries()->create('albums/category-1/album-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/root/albums/category-1/album-1/entry.yaml')->get()));

    entries()->create('banners', ['title' => 'Banners']);
    entries()->create('banners/1', ['title' => 'Banner1']);
    entries()->create('banners/2', ['title' => 'Banner2']);

    $root = entries()->fetch('root');

    $this->assertEquals(15, $root->count());
});

test('EntriesField for long nested entries', function () {
    entries()->create('level1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/level1/entry.yaml')->get()));
    entries()->create('level1/level2', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/level1/level2/entry.yaml')->get()));
    entries()->create('level1/level2/level3', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/level1/level2/level3/entry.yaml')->get()));
    entries()->create('level1/level2/level3/level4', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/level1/level2/level3/level4/entry.yaml')->get()));

    $level = entries()->fetch('level1');

    $this->assertEquals(13, $level->count());
    $this->assertEquals('level1/level2', $level['root']['id']);
    $this->assertEquals('level1/level2/level3', $level['root']['root']['id']);
    $this->assertEquals('level1/level2/level3/level4', $level['root']['root']['root']['id']);
});

test('EntriesField for macroable fetch entries', function () {
    entries()->create('macroable', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/macroable/entry.yaml')->get()));

    entries()::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    $macroable = entries()->fetch('macroable');

    $this->assertEquals('table', $macroable['table']['id']);
    $this->assertEquals('world', $macroable['table']['options']['hello']);
});
