<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test fetchField', function () {



    // Create catalog
    flextype('entries')->create('catalog', flextype('frontmatter')->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/catalog/entry.md')->get()));
    flextype('entries')->create('catalog/bikes', ['title' => 'Bikes']);
    flextype('entries')->create('catalog/bikes/gt', ['title' => 'GT', 'brand' => 'gt']);
    flextype('entries')->create('catalog/bikes/norco', ['title' => 'Norco', 'brand' => 'norco']);
    flextype('entries')->create('catalog/bikes/foo', ['title' => 'foo']);
    flextype('entries')->create('catalog/bikes/foo/bar', ['title' => 'bar']);

    // Create discounts
    flextype('entries')->create('discounts', ['title' => 'Discounts']);
    flextype('entries')->create('discounts/30-off', ['title' => '30% off', 'category' => 'bikes']);
    flextype('entries')->create('discounts/50-off', ['title' => '50% off', 'category' => 'bikes']);

    // Create banner
    flextype('entries')->create('banner', ['title' => 'Banner']);

    $catalogSingle = flextype('entries')->fetch('catalog');
    $this->assertEquals(15, $catalogSingle->count());
    $this->assertEquals('Catalog', $catalogSingle['title']);
    $this->assertEquals('catalog', $catalogSingle['id']);
    $this->assertEquals(1, $catalogSingle['bikes']->count());
    $this->assertTrue(isset($catalogSingle['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingle['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingle['discounts']->count());
    $this->assertTrue(isset($catalogSingle['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingle['discounts']['discounts/30-off']['title']);

    $catalogSingleWithCollectionFalse = flextype('entries')->fetch('catalog', 'single');
    $this->assertEquals(15, $catalogSingleWithCollectionFalse->count());
    $this->assertEquals('Catalog', $catalogSingleWithCollectionFalse['title']);
    $this->assertEquals('catalog', $catalogSingleWithCollectionFalse['id']);
    $this->assertEquals(1, $catalogSingleWithCollectionFalse['bikes']->count());
    $this->assertTrue(isset($catalogSingleWithCollectionFalse['bikes']['catalog/bikes/gt']));
    $this->assertEquals('GT', $catalogSingleWithCollectionFalse['bikes']['catalog/bikes/gt']['title']);
    $this->assertEquals(1, $catalogSingleWithCollectionFalse['discounts']->count());
    $this->assertTrue(isset($catalogSingleWithCollectionFalse['discounts']['discounts/30-off']));
    $this->assertEquals('30% off', $catalogSingleWithCollectionFalse['discounts']['discounts/30-off']['title']);

    $catalogCollection = flextype('entries')->fetch('catalog', 'collection');
    $this->assertEquals(1, $catalogCollection->count());
    $this->assertEquals('Bikes', $catalogCollection['catalog/bikes']['title']);
    $this->assertEquals('catalog/bikes', $catalogCollection['catalog/bikes']['id']);

    $catalogLongCollecion = flextype('entries')->fetch('catalog', 'collection', ['find' => ['depth' => ['>0', '<4']]]);
    $this->assertEquals(5, $catalogLongCollecion->count());

    $banner = flextype('entries')->fetch('banner');
    $this->assertEquals('Banner', $banner['title']);
    $this->assertEquals('banner', $banner['id']);
});
