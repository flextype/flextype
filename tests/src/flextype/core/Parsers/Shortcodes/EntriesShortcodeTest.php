<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('[entries-fetch] shortcode', function () {
    $this->assertTrue(entries()->create('foo', ['title' => 'Foo']));
    $this->assertEquals('Foo', parsers()->shortcodes()->parse('[entries-fetch id="foo" field="title"]'));
    $this->assertEquals('Bar', parsers()->shortcodes()->parse('[entries-fetch id="foo" field="bar" default="Bar"]'));
});
