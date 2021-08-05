<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test SlugField', function () {
    entries()->create('foo', []);
    $slug = entries()->fetch('foo')['slug'];
    $this->assertEquals('foo', $slug);

    entries()->create('bar', []);
    $slug = entries()->fetch('bar')['slug'];
    $this->assertEquals('bar', $slug);
});
