<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('shortcodes directive', function () {
    entries()->create('shortcodes', ['foo' => '@shortcodes [strings prepend="Hello "]World[/strings]']);

    $this->assertEquals('Hello World', entries()->fetch('shortcodes')['foo']);
});