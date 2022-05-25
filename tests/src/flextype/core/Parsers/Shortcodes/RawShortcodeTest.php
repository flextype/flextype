<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('[raw] shortcode', function () {
    $this->assertTrue(entries()->create('foo', ['title' => 'Foo']));
    $this->assertEquals("(entries fetch:'foo' field:'title')",
                        parsers()->shortcodes()->parse("(raw)(entries fetch:'foo' field:'title')(/raw)"));
});
