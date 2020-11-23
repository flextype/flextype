<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertStringContainsString('http', flextype('shortcode')->process('[url]'));

    flextype('registry')->set('flextype.settings.url', 'https://flextype.org');
    $this->assertStringContainsString('https://flextype.org', flextype('shortcode')->process('[url]'));
});
