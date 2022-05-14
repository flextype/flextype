<?php

declare(strict_types=1);

test('[registry] shortcode', function () {
    expect(strings(parsers()->shortcodes()->parse('[registry get="flextype.manifest"]'))->isJson())->toBeTrue();
    expect(parsers()->shortcodes()->parse('[registry get="flextype.manifest.name"]'))->toBe('Flextype');
    expect(parsers()->shortcodes()->parse('[registry get="flextype.manifest.foo,Default"]'))->toBe('Default');

});
