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
    
    entries()->create('var', [
        'title' => 'Title',

        // Set
        'test-set-1' => '[% vars().set("test-set-1", "Foo!") %]',

        // Get
        'test-get-1' => '[[ vars().get("test-set-1") ]]',
        'test-get-2' => '[[ var("test-set-1") ]]',
    ]);

    expect(entries()->fetch('var')['test-get-1'])->toBe('Foo!');
    expect(entries()->fetch('var')['test-get-2'])->toBe('Foo!');
});