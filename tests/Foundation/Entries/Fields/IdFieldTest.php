<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test IdField', function () {
    flextype('entries')->create('foo', []);
    $id = flextype('entries')->fetch('foo')['id'];
    $this->assertEquals('foo', $id);

    flextype('entries')->create('foo/bar', []);
    $id = flextype('entries')->fetch('foo/bar')['id'];
    $this->assertEquals('foo/bar', $id);
});
