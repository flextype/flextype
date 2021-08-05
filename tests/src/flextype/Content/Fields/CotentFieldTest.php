<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test content field for blog', function () {
    content()->create('blog', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/blog/content.yaml')->get()));
    content()->create('blog/post-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/blog/post-1/content.yaml')->get()));
    content()->create('blog/post-2', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/blog/post-2/content.yaml')->get()));

    $blog = content()->fetch('blog');

    $this->assertEquals(13, $blog->count());
});

test('test content field for catalog', function () {

    // Create catalog
    content()->create('catalog', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/catalog/content.yaml')->get()));
    content()->create('catalog/bikes', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/catalog/bikes/content.yaml')->get()));
    content()->create('catalog/bikes/gt', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/catalog/bikes/gt/content.yaml')->get()));
    content()->create('catalog/bikes/norco', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/catalog/bikes/norco/content.yaml')->get()));
    content()->create('catalog/bikes/foo', ['title' => 'foo']);
    content()->create('catalog/bikes/foo/bar', ['title' => 'bar']);

    // Create discounts
    content()->create('discounts', ['title' => 'Discounts']);
    content()->create('discounts/30-off', ['title' => '30% off', 'category' => 'bikes']);
    content()->create('discounts/50-off', ['title' => '50% off', 'category' => 'bikes']);

    // Create banner
    content()->create('banner', ['title' => 'Banner']);

    $catalogSingle = content()->fetch('catalog');

    $this->assertEquals(15, $catalogSingle->count());
    $this->assertEquals('Catalog', $catalogSingle['title']);
    $this->assertEquals('catalog', $catalogSingle['id']);
    $this->assertEquals(1, $catalogSingle['bikes']->count());
    $this->assertTrue(isset($catalogSingle['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingle['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingle['discounts']->count());
    $this->assertTrue(isset($catalogSingle['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingle['discounts']['discounts/30-off']['title']);

    $catalogCollection = content()->fetch('catalog', ['collection' => true]);
    $this->assertEquals(1, $catalogCollection->count());
    $this->assertEquals('Bikes', $catalogCollection['catalog/bikes']['title']);
    $this->assertEquals('catalog/bikes', $catalogCollection['catalog/bikes']['id']);

    $catalogLongCollecion = content()->fetch('catalog', ['collection' => true, 'find' => ['depth' => ['>0', '<4']]]);
    $this->assertEquals(5, $catalogLongCollecion->count());

    $banner = content()->fetch('banner');
    $this->assertEquals('Banner', $banner['title']);
    $this->assertEquals('banner', $banner['id']);
});

test('test content field for albmus', function () {
    content()->create('root', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/root/content.yaml')->get()));

    content()->create('albums', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/root/albums/content.yaml')->get()));
    content()->create('albums/category-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/root/albums/category-1/content.yaml')->get()));
    content()->create('albums/category-1/album-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/root/albums/category-1/album-1/content.yaml')->get()));

    content()->create('banners', ['title' => 'Banners']);
    content()->create('banners/1', ['title' => 'Banner1']);
    content()->create('banners/2', ['title' => 'Banner2']);

    $root = content()->fetch('root');

    $this->assertEquals(15, $root->count());
});

test('test content field for long nested content', function () {
    content()->create('level1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/level1/content.yaml')->get()));
    content()->create('level1/level2', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/level1/level2/content.yaml')->get()));
    content()->create('level1/level2/level3', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/level1/level2/level3/content.yaml')->get()));
    content()->create('level1/level2/level3/level4', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/level1/level2/level3/level4/content.yaml')->get()));

    $level = content()->fetch('level1');

    $this->assertEquals(13, $level->count());
    $this->assertEquals('level1/level2', $level['root']['id']);
    $this->assertEquals('level1/level2/level3', $level['root']['root']['id']);
    $this->assertEquals('level1/level2/level3/level4', $level['root']['root']['root']['id']);
});

test('test content field for macroable fetch content', function () {
    content()->create('macroable', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/content/macroable/content.yaml')->get()));

    content()::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    $macroable = content()->fetch('macroable');

    $this->assertEquals('table', $macroable['table']['id']);
    $this->assertEquals('world', $macroable['table']['options']['hello']);
});
