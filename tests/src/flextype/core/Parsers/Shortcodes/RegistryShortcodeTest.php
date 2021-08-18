<?php

declare(strict_types=1);

test('[registry_get] shortcode', function () {
    $this->assertEquals('Flextype',
                        parsers()->shortcodes()->parse('[registry_get name="flextype.manifest.name"]'));
    $this->assertEquals('default-value',
                        parsers()->shortcodes()->parse('[registry_get name="item-name" default="default-value"]'));
});
