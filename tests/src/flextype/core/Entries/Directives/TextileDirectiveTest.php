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

test('textile directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.textile.enabled', false);
    entries()->create('textile', ['foo' => '@textile **Hello world!**']);
    $this->assertEquals('@textile **Hello world!**', entries()->fetch('textile')['foo']);
    registry()->set('flextype.settings.entries.directives.textile.enabled', true);
});