<?php

declare(strict_types=1);

test('[url] shortcode', function () {
    registry()->set('flextype.settings.url', 'https://flextype.org');

    $this->assertStringContainsString('https://flextype.org', parsers()->shortcodes()->parse('[url]'));
});
