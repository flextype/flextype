<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('strings expression', function () {
    entries()->create('strings', ['test' => '[[ strings("Foo").lower() ]]']);
    expect(entries()->fetch('strings')['test'])->toBe('foo');
});