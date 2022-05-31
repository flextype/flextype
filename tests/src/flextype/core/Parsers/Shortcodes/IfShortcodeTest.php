<?php

declare(strict_types=1);

test('if shortcode', function () {
    expect(parsers()->shortcodes()->parse("(if:'2 > 1')yes(/if)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(if:'2>1')yes(/if)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(if:'2<1')yes(/if)"))->toBe("");
});

test('if shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.if.enabled', false);
    expect(parsers()->shortcodes()->parse("(if:'2 > 1')yes(/if)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.if.enabled', true);
 });