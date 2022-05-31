<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('calc shortcode', function () {
    expect(entries()->create('foo', ['price' => '(calc:2+2)']))->toBeTrue();
    expect(entries()->fetch('foo')['price'])->toBe('4');
});

test('calc shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.calc.enabled', false);
    expect(entries()->create('foo', ['price' => '(calc:2+2)']))->toBeTrue();
    expect(entries()->fetch('foo')['price'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.calc.enabled', true);
});