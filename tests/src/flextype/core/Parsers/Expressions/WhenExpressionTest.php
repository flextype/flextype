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

test('when expression', function () {
    
    entries()->create('when', [
        'title' => 'Title',

        'test-when-positive' => '[[ when(title == "Title", "Yes!") ]]',
        'test-when-negative' => '[[ when(title == "Foo", "No!") ]]'
    ]);

    expect(entries()->fetch('when')['test-when-positive'])->toBe('Yes!');
    expect(entries()->fetch('when')['test-when-negative'])->toBe('No!');
});