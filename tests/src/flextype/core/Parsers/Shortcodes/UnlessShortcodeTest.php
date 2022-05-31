<?php

declare(strict_types=1);

test('unless shortcode', function () {
    expect(parsers()->shortcodes()->parse("(unless:'2 > 1')yes(/unless)"))->toBe("");
    expect(parsers()->shortcodes()->parse("(unless:'2>1')yes(/unless)"))->toBe("");
    expect(parsers()->shortcodes()->parse("(unless:'2<1')yes(/unless)"))->toBe("yes");
});

test('unless shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.unless.enabled', false);
    expect(parsers()->shortcodes()->parse("(unless:'2 > 1')yes(/unless)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.unless.enabled', true);
 });