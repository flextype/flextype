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

test('eval shortcode', function () {
    expect(entries()->create('foo', ['price' => '(eval:2+2) (eval)2+2(/eval)']))->toBeTrue();
    expect(entries()->fetch('foo')['price'])->toBe('4 4');
});

test('eval shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.eval.enabled', false);
    expect(entries()->create('foo', ['test' => '(eval:2+2) (eval)2+2(/eval)']))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe(' ');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.eval.enabled', true);
});