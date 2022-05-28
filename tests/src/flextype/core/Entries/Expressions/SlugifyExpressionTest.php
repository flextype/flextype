<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('slugify expression', function () {
    entries()->create('slugify', ['test' => '[[ slugify().slugify("foo bar") ]]']);
    expect(entries()->fetch('slugify')['test'])->toBe('foo-bar');
});