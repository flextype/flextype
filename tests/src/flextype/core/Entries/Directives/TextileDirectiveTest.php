<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('textile directive', function () {
    entries()->create('textile', ['foo' => '@textile **Hello world!**']);

    $this->assertEquals('<p> <b>Hello world!</b></p>', entries()->fetch('textile')['foo']);
});