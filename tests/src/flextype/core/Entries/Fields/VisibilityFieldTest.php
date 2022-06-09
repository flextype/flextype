<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('VisibilityField', function () {
    entries()->create('foo', []);
    $visibility = entries()->fetch('foo')['visibility'];
    $this->assertEquals('visible', $visibility);

    entries()->create('bar', ['visibility' => 'draft']);
    $visibility = entries()->fetch('bar')['visibility'];
    $this->assertEquals('draft', $visibility);

    entries()->create('zed', ['visibility' => 'foobar']);
    $visibility = entries()->fetch('zed')['visibility'];
    $this->assertEquals('visible', $visibility);
});
