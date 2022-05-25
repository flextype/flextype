<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('field shortcode', function () {
    expect(entries()->create('foo', ['title' => '(field:id)']))->toBeTrue();
    expect(entries()->fetch('foo')['title'])->toBe('foo');
});