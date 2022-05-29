<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('var expression', function () {
    entries()->create('var', ['vars' => ['foo' => 'Foo'], 'test' => '[[ var("foo") ]]']);
    expect(entries()->fetch('var')['test'])->toBe('Foo');
});