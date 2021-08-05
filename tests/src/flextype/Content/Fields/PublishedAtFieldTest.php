<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test PublishedAtField', function () {
    content()->create('foo', []);

    $published_at = content()->fetch('foo')['published_at'];

    $this->assertTrue(strlen($published_at) > 0);
    $this->assertTrue((ctype_digit($published_at) && strtotime(date('Y-m-d H:i:s', $published_at)) === (int)$published_at));
});
