<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertStringContainsString('http', parsers()->shortcode()->process('[url]'));

    registry()->set('flextype.settings.url', 'https://flextype.org');
    $this->assertStringContainsString('https://flextype.org', parsers()->shortcode()->process('[url]'));
});
