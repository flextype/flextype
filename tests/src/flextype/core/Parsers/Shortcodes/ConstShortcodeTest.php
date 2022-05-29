<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('const shortcode', function () {
    define('foo', 'Foo');
    expect(entries()->create('const', ['test' => '(const:foo)']))->toBeTrue();
    expect(entries()->fetch('const')['test'])->toBe('Foo');
});