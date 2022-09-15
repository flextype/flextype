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

test('const expression', function () {
    define('test-expression-const', 'Foo');
    entries()->create('const', ['test' => '[[ const("test-expression-const") ]]']);
    expect(entries()->fetch('const')['test'])->toBe('Foo');
});