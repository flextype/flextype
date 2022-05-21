<?php

declare(strict_types=1);

test('[tr] shortcode', function () {
   expect(parsers()->shortcodes()->parse('(tr find:foo)'))->toBe('foo');
});
