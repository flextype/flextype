<?php

declare(strict_types=1);

test('test registry_get shortcode', function () {
    $this->assertEquals('Flextype',
                        flextype('parsers')->shortcode()->process('[registry_get name="flextype.manifest.name"]'));
    $this->assertEquals('default-value',
                        flextype('parsers')->shortcode()->process('[registry_get name="item-name" default="default-value"]'));
});
