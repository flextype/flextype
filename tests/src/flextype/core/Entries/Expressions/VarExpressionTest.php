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

test('var expression', function () {
    entries()->create('var', ['vars' => ['foo' => 'Foo'], 'test' => '[[ var("foo") ]]']);
    expect(entries()->fetch('var')['test'])->toBe('Foo');
});