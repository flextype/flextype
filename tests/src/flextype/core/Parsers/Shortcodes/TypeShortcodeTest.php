<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('type shortcode', function () {
    expect(entries()->create('test-1', ['price' => '(type:int) 10']))->toBeTrue();
    expect(entries()->fetch('test-1')['price'])->toBe(10);

    expect(entries()->create('test-2', ['price' => '(type:string) 10']))->toBeTrue();
    expect(entries()->fetch('test-2')['price'])->toBe('10');
});