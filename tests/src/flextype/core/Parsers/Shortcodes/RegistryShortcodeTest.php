<?php

declare(strict_types=1);

test('[registry-get] shortcode', function () {
    $this->assertEquals('Flextype',
                        parsers()->shortcodes()->parse('[registry-get id="flextype.manifest.name"]'));
    $this->assertEquals('default-value',
                        parsers()->shortcodes()->parse('[registry-get id="item-name" default="default-value"]'));

    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.registry.enabled', false);
    $this->assertEquals('', parsers()->shortcodes()->parse('[registry-get id="item-name" default="default-value"]'));
});
