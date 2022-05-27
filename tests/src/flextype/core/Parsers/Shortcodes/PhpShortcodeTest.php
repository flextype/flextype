<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('php shortcode', function () {
    $this->assertEquals("Foo", parsers()->shortcodes()->parse('(php)echo "Foo";(/php)'));

    expect(entries()->create('bar', ['test' => '(php) echo "Bar";']))->toBeTrue();
    expect(entries()->fetch('bar')['test'])->toBe('Bar');
});
