<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('registry expression', function () {
    registry()->set('foo', 'Foo');
    entries()->create('registry', ['test' => '[[ registry().get("foo") ]]']);
    expect(entries()->fetch('registry')['test'])->toBe('Foo');
});