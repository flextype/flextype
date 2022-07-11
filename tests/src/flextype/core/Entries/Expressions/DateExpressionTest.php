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

test('date expression', function () {
    $date = date("F j, Y, g:i a");
    entries()->create('date', ['test' => '[[ date("F j, Y, g:i a") ]]']);
    expect(entries()->fetch('date')['test'])->toBe($date);
});