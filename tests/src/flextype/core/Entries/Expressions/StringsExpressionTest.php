<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('strings expression', function () {
    entries()->create('strings', ['test' => '[[ strings("Foo").lower() ]]']);
    expect(entries()->fetch('strings')['test'])->toBe('foo');
});