<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test ModifiedAtField', function () {
    flextype('entries')->create('foo', []);

    $modified_at = flextype('entries')->fetch('foo')['modified_at'];

    $this->assertTrue(strlen($modified_at) > 0);
    $this->assertTrue((ctype_digit($modified_at) && strtotime(date('Y-m-d H:i:s', $modified_at)) === (int)$modified_at));
});
