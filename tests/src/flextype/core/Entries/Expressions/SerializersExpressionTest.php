<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('serializers expression', function () {
    entries()->create('serializers', ['test' => '[[ collection(serializers().json().decode("{\"foo\": \"Foo\"}")).get("foo") ]]']);
    expect(trim(entries()->fetch('serializers')['test']))->toBe('Foo');
});