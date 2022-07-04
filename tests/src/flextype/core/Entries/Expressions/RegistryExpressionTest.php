<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('registry expression', function () {
    registry()->set('foo', 'Foo');
    entries()->create('registry', ['test' => '[[ registry().get("foo") ]]']);
    expect(entries()->fetch('registry')['test'])->toBe('Foo');
});