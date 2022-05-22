<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('calc directive', function () {
    registry()->set('flextype.settings.entries.directives.calc.enabled', true);
    entries()->create('field', ['foo' => '@type[int] @calc[2+2]']);
    expect(entries()->fetch('field')['foo'])->toBe(4);
});

test('calc directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.calc.enabled', false);
    entries()->create('field', ['foo' => '@calc[2+2]']);
    expect(entries()->fetch('field')['foo'])->toBe('@calc[2+2]');
    registry()->set('flextype.settings.entries.directives.calc.enabled', true);
});