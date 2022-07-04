<?php

declare(strict_types=1);

use function Glowy\Strings\strings;

test('registry shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse("(registry get id:'flextype.manifest')"))->isJson())->toBeTrue();
    expect(parsers()->shortcodes()->parse("(registry get id:'flextype.manifest.name')"))->toBe('Flextype');
    expect(parsers()->shortcodes()->parse("(registry get id:'flextype.manifest.foo' default:'Default')"))->toBe('Default');
});

test('registry shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled', false);
    expect(parsers()->shortcodes()->parse("(registry get id:'flextype.manifest.name')"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled', true);
});