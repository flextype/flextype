<?php

use Flextype\Component\Filesystem\Filesystem;

use function Glowy\Filesystem\filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('SlugField', function () {
    entries()->create('foo', []);
    $slug = entries()->fetch('foo')['slug'];
    $this->assertEquals('foo', $slug);

    entries()->create('bar', []);
    $slug = entries()->fetch('bar')['slug'];
    $this->assertEquals('bar', $slug);
});
