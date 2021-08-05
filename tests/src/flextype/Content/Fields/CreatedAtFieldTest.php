<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test CreatedAtField', function () {
    // 1
    content()->create('foo', []);
    $created_at = content()->fetch('foo')['created_at'];
    $this->assertTrue(strlen($created_at) > 0);
    $this->assertTrue((ctype_digit($created_at) && strtotime(date('Y-m-d H:i:s', $created_at)) === (int)$created_at));
});
