<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('field expression', function () {
    entries()->create('field', ['test' => '[[ field("id") ]]']);
    expect(entries()->fetch('field')['test'])->toBe('field');
});