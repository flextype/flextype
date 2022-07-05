<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;
use function Flextype\entries;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('slugify expression', function () {
    entries()->create('slugify', ['test' => '[[ slugify().slugify("foo bar") ]]']);
    expect(entries()->fetch('slugify')['test'])->toBe('foo-bar');
});