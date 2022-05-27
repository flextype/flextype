<?php

declare(strict_types=1);

test('if shortcode', function () {
    expect(parsers()->shortcodes()->parse("(if:'2 > 1')yes(/if)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(if:'2>1')yes(/if)"))->toBe("yes");
    expect(parsers()->shortcodes()->parse("(if:'2<1')yes(/if)"))->toBe("");
});
