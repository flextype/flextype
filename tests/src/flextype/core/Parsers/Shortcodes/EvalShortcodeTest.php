<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('eval shortcode', function () {
    expect(entries()->create('foo', ['price' => '(eval:2+2) (eval)2+2(/eval)']))->toBeTrue();
    expect(entries()->fetch('foo')['price'])->toBe('4 4');
});