<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test SlugField', function () {
    flextype('entries')->create('foo', []);
    $slug = flextype('entries')->fetch('foo')['slug'];
    $this->assertEquals('foo', $slug);

    flextype('entries')->create('bar', []);
    $slug = flextype('entries')->fetch('bar')['slug'];
    $this->assertEquals('bar', $slug);
});
