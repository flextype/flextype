<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('markdown directive', function () {
    entries()->create('markdown', ['foo' => '@markdown **Hello world!**']);

    $this->assertEquals('<p> <strong>Hello world!</strong></p>', entries()->fetch('markdown')['foo']);
});

test('markdown directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.markdown.enabled', false);
    entries()->create('markdown', ['foo' => '@markdown **Hello world!**']);
    $this->assertEquals('@markdown **Hello world!**', entries()->fetch('markdown')['foo']);
    registry()->set('flextype.settings.entries.directives.markdown.enabled', true);
});