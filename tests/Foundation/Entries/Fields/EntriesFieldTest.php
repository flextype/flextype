<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test entries field for blog', function () {
    flextype('entries')->create('blog', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/blog/entry.md')->get()));
    flextype('entries')->create('blog/post-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/blog/post-1/entry.md')->get()));
    flextype('entries')->create('blog/post-2', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/blog/post-2/entry.md')->get()));

    $blog = flextype('entries')->fetch('blog');

    $this->assertEquals(14, $blog->count());
});

test('test entries field for catalog', function () {

    // Create catalog
    flextype('entries')->create('catalog', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/catalog/entry.md')->get()));
    flextype('entries')->create('catalog/bikes', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/catalog/bikes/entry.md')->get()));
    flextype('entries')->create('catalog/bikes/gt', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/catalog/bikes/gt/entry.md')->get()));
    flextype('entries')->create('catalog/bikes/norco', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/catalog/bikes/norco/entry.md')->get()));
    flextype('entries')->create('catalog/bikes/foo', ['title' => 'foo']);
    flextype('entries')->create('catalog/bikes/foo/bar', ['title' => 'bar']);

    // Create discounts
    flextype('entries')->create('discounts', ['title' => 'Discounts']);
    flextype('entries')->create('discounts/30-off', ['title' => '30% off', 'category' => 'bikes']);
    flextype('entries')->create('discounts/50-off', ['title' => '50% off', 'category' => 'bikes']);

    // Create banner
    flextype('entries')->create('banner', ['title' => 'Banner']);

    $catalogSingle = flextype('entries')->fetch('catalog');

    $this->assertEquals(16, $catalogSingle->count());
    $this->assertEquals('Catalog', $catalogSingle['title']);
    $this->assertEquals('catalog', $catalogSingle['id']);
    $this->assertEquals(1, $catalogSingle['bikes']->count());
    $this->assertTrue(isset($catalogSingle['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingle['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingle['discounts']->count());
    $this->assertTrue(isset($catalogSingle['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingle['discounts']['discounts/30-off']['title']);

    $catalogCollection = flextype('entries')->fetch('catalog', ['collection' => true]);
    $this->assertEquals(1, $catalogCollection->count());
    $this->assertEquals('Bikes', $catalogCollection['catalog/bikes']['title']);
    $this->assertEquals('catalog/bikes', $catalogCollection['catalog/bikes']['id']);

    $catalogLongCollecion = flextype('entries')->fetch('catalog', ['collection' => true, 'find' => ['depth' => ['>0', '<4']]]);
    $this->assertEquals(5, $catalogLongCollecion->count());

    $banner = flextype('entries')->fetch('banner');
    $this->assertEquals('Banner', $banner['title']);
    $this->assertEquals('banner', $banner['id']);
});

test('test entries field for albmus', function () {
    flextype('entries')->create('root', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/root/entry.md')->get()));

    flextype('entries')->create('albums', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/root/albums/entry.md')->get()));
    flextype('entries')->create('albums/category-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/root/albums/category-1/entry.md')->get()));
    flextype('entries')->create('albums/category-1/album-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/root/albums/category-1/album-1/entry.md')->get()));

    flextype('entries')->create('banners', ['title' => 'Banners']);
    flextype('entries')->create('banners/1', ['title' => 'Banner1']);
    flextype('entries')->create('banners/2', ['title' => 'Banner2']);

    $root = flextype('entries')->fetch('root');

    $this->assertEquals(16, $root->count());
});

test('test entries field for long nested entries', function () {
    flextype('entries')->create('level1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/level1/entry.md')->get()));
    flextype('entries')->create('level1/level2', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/level1/level2/entry.md')->get()));
    flextype('entries')->create('level1/level2/level3', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/level1/level2/level3/entry.md')->get()));
    flextype('entries')->create('level1/level2/level3/level4', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/level1/level2/level3/level4/entry.md')->get()));

    $level = flextype('entries')->fetch('level1');

    $this->assertEquals(14, $level->count());
    $this->assertEquals('level1/level2', $level['root']['id']);
    $this->assertEquals('level1/level2/level3', $level['root']['root']['id']);
    $this->assertEquals('level1/level2/level3/level4', $level['root']['root']['root']['id']);
});

test('test entries field for macroable fetch entries', function () {
    flextype('entries')->create('macroable', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/macroable/entry.md')->get()));

    flextype('entries')::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    $macroable = flextype('entries')->fetch('macroable');

    $this->assertEquals('table', $macroable['table']['id']);
    $this->assertEquals('world', $macroable['table']['options']['hello']);
});
