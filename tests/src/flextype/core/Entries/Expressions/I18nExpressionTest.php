<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('i18n expression', function () {
    entries()->create('i18n', ['test' => '[[ __("foo") ~ tr("bar") ]]']);
    expect(entries()->fetch('i18n')['test'])->toBe('foobar');
});