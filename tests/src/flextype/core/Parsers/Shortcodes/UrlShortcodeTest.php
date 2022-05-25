<?php

declare(strict_types=1);

test('(getBaseUrl) shortcode', function () {
    registry()->set('flextype.settings.base_url', 'https://awilum.github.io/flextype');

    $this->assertStringContainsString('https://awilum.github.io/flextype', parsers()->shortcodes()->parse('(getBaseUrl)'));
});

test('(getBasePath) shortcode', function () {
    app()->setBasePath('/foo/');

    $this->assertStringContainsString('/foo/', parsers()->shortcodes()->parse('(getBasePath)'));
});

test('(getAbsoluteUrl) shortcode', function () {
    $this->assertStringContainsString('/', parsers()->shortcodes()->parse('(getAbsoluteUrl)'));
});

test('(getUriString) shortcode', function () {
    $this->assertStringContainsString('', parsers()->shortcodes()->parse('(getUriString)'));
});

test('(urlFor) shortcode', function () {
    $this->assertStringContainsString('', parsers()->shortcodes()->parse("(urlFor routeName='test-route' queryParams='{\"foo\": \"Foo\"}')"));
});