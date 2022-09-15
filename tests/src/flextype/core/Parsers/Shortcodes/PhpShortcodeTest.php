<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;
use function Flextype\parsers;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('php shortcode', function () {
    $this->assertEquals("Foo", parsers()->shortcodes()->parse('(php)echo "Foo";(/php)'));

    expect(entries()->create('bar', ['test' => '(php) echo "Bar";']))->toBeTrue();
    expect(entries()->fetch('bar')['test'])->toBe('Bar');
});

test('php shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.php.enabled', false);
    expect(parsers()->shortcodes()->parse("(php)**Foo**(/php)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.php.enabled', true);
});