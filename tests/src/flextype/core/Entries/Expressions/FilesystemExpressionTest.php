<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('filesystem expression', function () {
    entries()->create('filesystem', ['test' => '[[ filesystem().file("1.txt").extension() ]]']);
    expect(entries()->fetch('filesystem')['test'])->toBe('txt');
});