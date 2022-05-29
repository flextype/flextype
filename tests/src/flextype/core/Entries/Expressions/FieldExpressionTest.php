<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('field expression', function () {
    entries()->create('field', ['test' => '[[ field("id") ]]']);
    expect(entries()->fetch('field')['test'])->toBe('field');
});