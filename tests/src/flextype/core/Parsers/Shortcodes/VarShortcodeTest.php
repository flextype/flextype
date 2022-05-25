<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('var shortcode', function () {
    expect(entries()->create('foo', ['vars' => ['foo' => 'Foo'], 'title' => '(var:foo)']))->toBeTrue();
    expect(entries()->fetch('foo')['title'])->toBe('Foo');
});