<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertStringContainsString('http', parsers()->shortcodes()->process('[url]'));

    registry()->set('flextype.settings.url', 'https://flextype.org');
    $this->assertStringContainsString('https://flextype.org', parsers()->shortcodes()->process('[url]'));
});
