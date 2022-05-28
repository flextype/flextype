<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('parsers expression', function () {
    entries()->create('parsers', ['test' => '[[ parsers().markdown().parse("**foo**") ]]']);
    expect(trim(entries()->fetch('parsers')['test']))->toBe('<p><strong>foo</strong></p>');
});