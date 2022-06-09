<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('field shortcode', function () {
    expect(entries()->create('foo', ['title' => '(field:id)']))->toBeTrue();
    expect(entries()->fetch('foo')['title'])->toBe('foo');
});

test('field shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.field.enabled', false);
    expect(entries()->create('foo', ['test' => '(field:id)']))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.field.enabled', true);
});