<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test SlugField', function () {
    content()->create('foo', []);
    $slug = content()->fetch('foo')['slug'];
    $this->assertEquals('foo', $slug);

    content()->create('bar', []);
    $slug = content()->fetch('bar')['slug'];
    $this->assertEquals('bar', $slug);
});
