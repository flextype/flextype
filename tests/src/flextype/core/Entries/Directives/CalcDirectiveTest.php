<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('calc directive', function () {
    // (calc:1+1)
    entries()->create('field', ['foo' => '@calc[2+2]']);
    $this->assertEquals(4, entries()->fetch('field')['foo']);
});