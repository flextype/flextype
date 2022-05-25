<?php

declare(strict_types=1);

test('uuid  shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse('(uuid1)'))->length() > 0)->toBeTrue();
    expect(strings(parsers()->shortcodes()->parse('(uuid2)'))->length() > 0)->toBeTrue();
    expect(strings(parsers()->shortcodes()->parse('(uuid3)'))->length() > 0)->toBeTrue();
    expect(strings(parsers()->shortcodes()->parse('(uuid4)'))->length() > 0)->toBeTrue();
});