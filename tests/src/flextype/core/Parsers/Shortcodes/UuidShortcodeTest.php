<?php

declare(strict_types=1);

use function Glowy\Strings\strings;

test('uuid shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse('(uuid)'))->length() > 0)->toBeTrue();
    expect(strings(parsers()->shortcodes()->parse('(uuid:4)'))->length() > 0)->toBeTrue();
});

test('uuid shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled', false);
    expect(parsers()->shortcodes()->parse("(uuid)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.uuid.enabled', true);
});