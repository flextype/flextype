<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test VisibilityField', function () {
    flextype('content')->create('foo', []);
    $visibility = flextype('content')->fetch('foo')['visibility'];
    $this->assertEquals('visible', $visibility);

    flextype('content')->create('bar', ['visibility' => 'draft']);
    $visibility = flextype('content')->fetch('bar')['visibility'];
    $this->assertEquals('draft', $visibility);

    flextype('content')->create('zed', ['visibility' => 'foobar']);
    $visibility = flextype('content')->fetch('zed')['visibility'];
    $this->assertEquals('visible', $visibility);
});
