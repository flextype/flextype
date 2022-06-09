<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('serializers expression', function () {
    entries()->create('serializers', ['test' => '[[ collection(serializers().json().decode("{\"foo\": \"Foo\"}")).get("foo") ]]']);
    expect(trim(entries()->fetch('serializers')['test']))->toBe('Foo');
});