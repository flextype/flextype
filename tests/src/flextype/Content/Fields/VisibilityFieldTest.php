<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test VisibilityField', function () {
    content()->create('foo', []);
    $visibility = content()->fetch('foo')['visibility'];
    $this->assertEquals('visible', $visibility);

    content()->create('bar', ['visibility' => 'draft']);
    $visibility = content()->fetch('bar')['visibility'];
    $this->assertEquals('draft', $visibility);

    content()->create('zed', ['visibility' => 'foobar']);
    $visibility = content()->fetch('zed')['visibility'];
    $this->assertEquals('visible', $visibility);
});
