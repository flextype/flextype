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

test('i18n expression', function () {
    entries()->create('i18n', ['test' => '[[ __("foo") ~ tr("bar") ]]']);
    expect(entries()->fetch('i18n')['test'])->toBe('foobar');
});