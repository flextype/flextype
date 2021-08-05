<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test entries_fetch  shortcode', function () {
    $this->assertTrue(entries()->create('foo', ['title' => 'Foo']));
    $this->assertEquals('Foo', parsers()->shortcodes()->process('[entries_fetch id="foo" field="title"]'));
    $this->assertEquals('Bar', parsers()->shortcodes()->process('[entries_fetch id="foo" field="bar" default="Bar"]'));
});
