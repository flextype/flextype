<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('date shortcode', function () {
    $date = date('F j, Y, g:i a');
    expect(entries()->create('date', ['date' => "(date:'F j, Y, g:i a')"]))->toBeTrue();
    expect(entries()->fetch('date')['date'])->toBe($date);
});