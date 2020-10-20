<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertStringContainsString('http', flextype('shortcode')->process('[url]'));
});
