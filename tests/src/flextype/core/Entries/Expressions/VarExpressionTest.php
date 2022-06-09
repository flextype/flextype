<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('var expression', function () {
    entries()->create('var', ['vars' => ['foo' => 'Foo'], 'test' => '[[ var("foo") ]]']);
    expect(entries()->fetch('var')['test'])->toBe('Foo');
});