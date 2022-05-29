<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('const expression', function () {
    define('test-expression-const', 'Foo');
    entries()->create('const', ['test' => '[[ const("test-expression-const") ]]']);
    expect(entries()->fetch('const')['test'])->toBe('Foo');
});