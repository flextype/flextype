<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('type shortcode', function () {
    expect(entries()->create('test-1', ['price' => '(type:int) 10']))->toBeTrue();
    expect(entries()->fetch('test-1')['price'])->toBe(10);

    expect(entries()->create('test-2', ['price' => '(type:string) 10']))->toBeTrue();
    expect(entries()->fetch('test-2')['price'])->toBe('10');
});

test('type shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.type.enabled', false);
    expect(parsers()->shortcodes()->parse("(type:int) 10"))->toBe(' 10');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.type.enabled', true);
});