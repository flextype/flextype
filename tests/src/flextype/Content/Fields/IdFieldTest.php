<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test IdField', function () {
    content()->create('foo', []);
    $id = content()->fetch('foo')['id'];
    $this->assertEquals('foo', $id);

    content()->create('foo/bar', []);
    $id = content()->fetch('foo/bar')['id'];
    $this->assertEquals('foo/bar', $id);
});
