<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test raw  shortcode', function () {
    $this->assertTrue(content()->create('foo', ['title' => 'Foo']));
    $this->assertEquals('[content_fetch id="foo" field="title"]',
                        parsers()->shortcodes()->parse('[raw][content_fetch id="foo" field="title"][/raw]'));
});
