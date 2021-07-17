<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test IdField', function () {
    flextype('content')->create('foo', []);
    $id = flextype('content')->fetch('foo')['id'];
    $this->assertEquals('foo', $id);

    flextype('content')->create('foo/bar', []);
    $id = flextype('content')->fetch('foo/bar')['id'];
    $this->assertEquals('foo/bar', $id);
});
