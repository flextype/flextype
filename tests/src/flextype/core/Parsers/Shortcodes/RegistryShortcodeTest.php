<?php

declare(strict_types=1);

test('[registry-get] shortcode', function () {
    $this->assertEquals('Flextype',
                        parsers()->shortcodes()->parse('[registry-get name="flextype.manifest.name"]'));
    $this->assertEquals('default-value',
                        parsers()->shortcodes()->parse('[registry-get name="item-name" default="default-value"]'));
});
