<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\parsers;
use function Flextype\registry;
use function Flextype\entries;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('raw shortcode', function () {
    $this->assertTrue(entries()->create('foo', ['title' => 'Foo']));
    $this->assertEquals("(entries fetch:'foo' field:'title')",
                        parsers()->shortcodes()->parse("(raw)(entries fetch:'foo' field:'title')(/raw)"));
});

test('raw shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.raw.enabled', false);
    expect(parsers()->shortcodes()->parse("(raw)(entries fetch:'foo' field:'title')(/raw)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.raw.enabled', true);
});