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

test('unless expression', function () {
    
    entries()->create('unless', [
        'title' => 'Title',

        'test-unless-positive' => '[[ unless(title == "Foo", "Yes!") ]]',
        'test-unless-negative' => '[[ unless(title == "Title", "Yes!") ]]'
    ]);

    expect(entries()->fetch('unless')['test-unless-positive'])->toBe('Yes!');
    expect(entries()->fetch('unless')['test-unless-negative'])->toBe('');
});