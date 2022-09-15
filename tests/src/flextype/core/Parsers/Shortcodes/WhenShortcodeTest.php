<?php

declare(strict_types=1);

use function Flextype\registry;
use function Flextype\parsers;

test('when shortcode', function () {
    expect(parsers()->shortcodes()->parse("(when:'2 > 1')yes(/when)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(when:'2>1')yes(/when)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(when:'2<1')yes(/when)"))->toBe("");
});

test('when shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.when.enabled', false);
    expect(parsers()->shortcodes()->parse("(when:'2 > 1')yes(/when)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.when.enabled', true);
 });