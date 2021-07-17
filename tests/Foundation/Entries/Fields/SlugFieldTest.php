<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test SlugField', function () {
    flextype('content')->create('foo', []);
    $slug = flextype('content')->fetch('foo')['slug'];
    $this->assertEquals('foo', $slug);

    flextype('content')->create('bar', []);
    $slug = flextype('content')->fetch('bar')['slug'];
    $this->assertEquals('bar', $slug);
});
