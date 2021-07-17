<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/content')->delete();
});

test('test content field for blog', function () {
    flextype('content')->create('blog', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/blog/entry.yaml')->get()));
    flextype('content')->create('blog/post-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/blog/post-1/entry.yaml')->get()));
    flextype('content')->create('blog/post-2', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/blog/post-2/entry.yaml')->get()));

    $blog = flextype('content')->fetch('blog');

    $this->assertEquals(14, $blog->count());
});

test('test content field for catalog', function () {

    // Create catalog
    flextype('content')->create('catalog', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/catalog/entry.yaml')->get()));
    flextype('content')->create('catalog/bikes', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/catalog/bikes/entry.yaml')->get()));
    flextype('content')->create('catalog/bikes/gt', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/catalog/bikes/gt/entry.yaml')->get()));
    flextype('content')->create('catalog/bikes/norco', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/catalog/bikes/norco/entry.yaml')->get()));
    flextype('content')->create('catalog/bikes/foo', ['title' => 'foo']);
    flextype('content')->create('catalog/bikes/foo/bar', ['title' => 'bar']);

    // Create discounts
    flextype('content')->create('discounts', ['title' => 'Discounts']);
    flextype('content')->create('discounts/30-off', ['title' => '30% off', 'category' => 'bikes']);
    flextype('content')->create('discounts/50-off', ['title' => '50% off', 'category' => 'bikes']);

    // Create banner
    flextype('content')->create('banner', ['title' => 'Banner']);

    $catalogSingle = flextype('content')->fetch('catalog');

    $this->assertEquals(16, $catalogSingle->count());
    $this->assertEquals('Catalog', $catalogSingle['title']);
    $this->assertEquals('catalog', $catalogSingle['id']);
    $this->assertEquals(1, $catalogSingle['bikes']->count());
    $this->assertTrue(isset($catalogSingle['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingle['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingle['discounts']->count());
    $this->assertTrue(isset($catalogSingle['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingle['discounts']['discounts/30-off']['title']);

    $catalogCollection = flextype('content')->fetch('catalog', ['collection' => true]);
    $this->assertEquals(1, $catalogCollection->count());
    $this->assertEquals('Bikes', $catalogCollection['catalog/bikes']['title']);
    $this->assertEquals('catalog/bikes', $catalogCollection['catalog/bikes']['id']);

    $catalogLongCollecion = flextype('content')->fetch('catalog', ['collection' => true, 'find' => ['depth' => ['>0', '<4']]]);
    $this->assertEquals(5, $catalogLongCollecion->count());

    $banner = flextype('content')->fetch('banner');
    $this->assertEquals('Banner', $banner['title']);
    $this->assertEquals('banner', $banner['id']);
});

test('test content field for albmus', function () {
    flextype('content')->create('root', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/root/entry.yaml')->get()));

    flextype('content')->create('albums', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/root/albums/entry.yaml')->get()));
    flextype('content')->create('albums/category-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/root/albums/category-1/entry.yaml')->get()));
    flextype('content')->create('albums/category-1/album-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/root/albums/category-1/album-1/entry.yaml')->get()));

    flextype('content')->create('banners', ['title' => 'Banners']);
    flextype('content')->create('banners/1', ['title' => 'Banner1']);
    flextype('content')->create('banners/2', ['title' => 'Banner2']);

    $root = flextype('content')->fetch('root');

    $this->assertEquals(16, $root->count());
});

test('test content field for long nested content', function () {
    flextype('content')->create('level1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/level1/entry.yaml')->get()));
    flextype('content')->create('level1/level2', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/level1/level2/entry.yaml')->get()));
    flextype('content')->create('level1/level2/level3', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/level1/level2/level3/entry.yaml')->get()));
    flextype('content')->create('level1/level2/level3/level4', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/level1/level2/level3/level4/entry.yaml')->get()));

    $level = flextype('content')->fetch('level1');

    $this->assertEquals(14, $level->count());
    $this->assertEquals('level1/level2', $level['root']['id']);
    $this->assertEquals('level1/level2/level3', $level['root']['root']['id']);
    $this->assertEquals('level1/level2/level3/level4', $level['root']['root']['root']['id']);
});

test('test content field for macroable fetch content', function () {
    flextype('content')->create('macroable', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/content/macroable/entry.yaml')->get()));

    flextype('content')::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    $macroable = flextype('content')->fetch('macroable');

    $this->assertEquals('table', $macroable['table']['id']);
    $this->assertEquals('world', $macroable['table']['options']['hello']);
});
