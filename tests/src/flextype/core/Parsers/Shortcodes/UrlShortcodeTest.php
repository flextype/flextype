<?php

declare(strict_types=1);

use function Flextype\parsers;
use function Flextype\registry;
use function Flextype\app;

test('getBaseUrl shortcode', function () {
    registry()->set('flextype.settings.base_url', 'https://awilum.github.io/flextype');

    $this->assertStringContainsString('https://awilum.github.io/flextype', parsers()->shortcodes()->parse('(getBaseUrl)'));
});

test('getBasePath shortcode', function () {
    app()->setBasePath('/foo/');

    $this->assertStringContainsString('/foo/', parsers()->shortcodes()->parse('(getBasePath)'));
});

test('getAbsoluteUrl shortcode', function () {
    $this->assertStringContainsString('/', parsers()->shortcodes()->parse('(getAbsoluteUrl)'));
});

test('getUriString shortcode', function () {
    $this->assertStringContainsString('', parsers()->shortcodes()->parse('(getUriString)'));
});

test('urlFor shortcode', function () {
    $this->assertStringContainsString('', parsers()->shortcodes()->parse("(urlFor routeName='test-route' queryParams='{\"foo\": \"Foo\"}')"));
});

test('url shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.url.enabled', false);
    expect(parsers()->shortcodes()->parse("(getBasePath)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.url.enabled', true);
});