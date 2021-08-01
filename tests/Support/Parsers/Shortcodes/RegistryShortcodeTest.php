<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertEquals('Flextype',
                        parsers()->shortcodes()->process('[registry_get name="flextype.manifest.name"]'));
    $this->assertEquals('default-value',
                        parsers()->shortcodes()->process('[registry_get name="item-name" default="default-value"]'));
});
