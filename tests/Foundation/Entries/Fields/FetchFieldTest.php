<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test fetchField', function () {
    flextype('entries')->create('bikes', ['title' => 'Bikes']);
    flextype('entries')->create('bikes/gt', ['title' => 'GT', 'brand' => 'gt']);
    flextype('entries')->create('bikes/norco', ['title' => 'Norco', 'brand' => 'norco']);

    flextype('entries')->create('discounts', ['title' => 'Discounts']);
    flextype('entries')->create('discounts/30-off', ['title' => '30% off', 'visibility' => 'published']);
    flextype('entries')->create('discounts/50-off', ['title' => '50% off']);

    flextype('entries')->create('banner', ['title' => 'Banner']);

    flextype('entries')->create('catalog',
flextype('yaml')->decode("title: Catalog
fetch:
  -
    id: bikes
    result: bikes
    options:
      collection: true
      where:
        -
          key: brand
          operator: eq
          value: gt
      limit: 10
  -
    id: discounts
    result: discounts
    options:
      collection: true
      where:
        -
          key: title
          operator: eq
          value: '30% off'
        -
          key: visibility
          operator: eq
          value: published
  -
    id: banner
    result: banner
")
);

    $fetch = flextype('entries')->fetch('catalog');


    //$this->assertEquals(1, $fetch['discounts']->count());
    $this->assertEquals('Banner', $fetch['banner']['title']);
});
