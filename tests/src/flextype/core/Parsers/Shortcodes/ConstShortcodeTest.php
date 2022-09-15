<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('const shortcode', function () {
    define('foo', 'Foo');
    expect(entries()->create('const', ['test' => '(const:foo)']))->toBeTrue();
    expect(entries()->fetch('const')['test'])->toBe('Foo');
});


test('const shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.const.enabled', false);
    expect(entries()->create('foo', ['test' => '(const:foo)']))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.const.enabled', true);
});