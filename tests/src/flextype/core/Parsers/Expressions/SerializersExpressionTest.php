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

test('serializers expression', function () {
    entries()->create('serializers', ['test' => '[[ collection(serializers().json().decode("{\"foo\": \"Foo\"}")).get("foo") ]]']);
    expect(trim(entries()->fetch('serializers')['test']))->toBe('Foo');
});