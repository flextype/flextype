<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test content_fetch  shortcode', function () {
    $this->assertTrue(content()->create('foo', ['title' => 'Foo']));
    $this->assertEquals('Foo', parsers()->shortcodes()->parse('[content_fetch id="foo" field="title"]'));
    $this->assertEquals('Bar', parsers()->shortcodes()->parse('[content_fetch id="foo" field="bar" default="Bar"]'));
});
