<?php

declare(strict_types=1);

test('[url] shortcode', function () {
    registry()->set('flextype.settings.url', 'https://awilum.github.io/flextype');

    $this->assertStringContainsString('https://awilum.github.io/flextype', parsers()->shortcodes()->parse('[url]'));
});
