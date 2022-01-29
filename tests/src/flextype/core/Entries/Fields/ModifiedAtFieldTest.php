<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('ModifiedAtField', function () {
    entries()->create('foo', []);

    $modified_at = entries()->fetch('foo')['modified_at'];

    $this->assertTrue(strlen($modified_at) > 0);
    $this->assertTrue((strtotime(date('Y-m-d H:i:s', $modified_at)) === (int)$modified_at));
});
