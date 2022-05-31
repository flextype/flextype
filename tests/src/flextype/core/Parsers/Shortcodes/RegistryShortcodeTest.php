<?php

declare(strict_types=1);

test('registry shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse("(registry get:'flextype.manifest')"))->isJson())->toBeTrue();
    expect(parsers()->shortcodes()->parse("(registry get:'flextype.manifest.name')"))->toBe('Flextype');
    expect(parsers()->shortcodes()->parse("(registry get:'flextype.manifest.foo' default:'Default')"))->toBe('Default');
});

test('registry shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled', false);
    expect(parsers()->shortcodes()->parse("(registry get:'flextype.manifest.name')"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled', true);
});