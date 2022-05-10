<?php

declare(strict_types=1);

test('[tr] shortcode', function () {
   expect(parsers()->shortcodes()->parse('[tr find="Foo"]'))->toBe('Foo');
});
