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

test('textile shortcode', function () {
    $this->assertEquals("<p><b>Foo</b></p>", parsers()->shortcodes()->parse('(textile)**Foo**(/textile)'));

    expect(entries()->create('textile', ['test' => '(textile) **Foo**']))->toBeTrue();
    expect(entries()->fetch('textile')['test'])->toBe("<p> <b>Foo</b></p>");
});

test('textile shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.textile.enabled', false);
    expect(parsers()->shortcodes()->parse("(textile)foo(/textile)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.textile.enabled', true);
});