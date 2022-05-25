<?php

declare(strict_types=1);

test('uuid shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse('(uuid)'))->length() > 0)->toBeTrue();
    expect(strings(parsers()->shortcodes()->parse('(uuid:4)'))->length() > 0)->toBeTrue();
});