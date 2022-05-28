<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('filesystem expression', function () {
    entries()->create('filesystem', ['test' => '[[ filesystem().file("1.txt").extension() ]]']);
    expect(entries()->fetch('filesystem')['test'])->toBe('txt');
});