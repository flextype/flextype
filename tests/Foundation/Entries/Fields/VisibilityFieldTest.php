<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test VisibilityField', function () {
    flextype('entries')->create('foo', []);
    $visibility = flextype('entries')->fetch('foo')['visibility'];
    $this->assertEquals('visible', $visibility);

    flextype('entries')->create('bar', ['visibility' => 'draft']);
    $visibility = flextype('entries')->fetch('bar')['visibility'];
    $this->assertEquals('draft', $visibility);

    flextype('entries')->create('zed', ['visibility' => 'foobar']);
    $visibility = flextype('entries')->fetch('zed')['visibility'];
    $this->assertEquals('visible', $visibility);
});
