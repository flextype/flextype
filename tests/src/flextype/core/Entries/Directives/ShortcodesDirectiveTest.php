<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('shortcodes directive', function () {
    entries()->create('shortcodes', ['foo' => "@shortcodes (strings prepend:'Hello ')World(/strings)"]);

    $this->assertEquals('Hello World', entries()->fetch('shortcodes')['foo']);
});

test('shortcodes directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.shortcodes.enabled', false);
    entries()->create('shortcodes', ['foo' => "@shortcodes (strings prepend:'Hello ')World(/strings)"]);
    $this->assertEquals("@shortcodes (strings prepend:'Hello ')World(/strings)", entries()->fetch('shortcodes')['foo']);
    registry()->set('flextype.settings.entries.directives.shortcodes.enabled', true);
});