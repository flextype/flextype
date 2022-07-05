<?php

declare(strict_types=1);

use function Flextype\parsers;
use function Flextype\registry;

test('tr shortcode', function () {
   expect(parsers()->shortcodes()->parse('(tr:foo)'))->toBe('foo');
});

test('tr shortcode disabled', function () {
   registry()->set('flextype.settings.parsers.shortcodes.shortcodes.i18n.enabled', false);
   expect(parsers()->shortcodes()->parse('(tr:foo)'))->toBe('');
   registry()->set('flextype.settings.parsers.shortcodes.shortcodes.i18n.enabled', true);
});