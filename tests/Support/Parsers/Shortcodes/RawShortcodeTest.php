<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test raw  shortcode', function () {
    $this->assertTrue(flextype('entries')->create('foo', ['title' => 'Foo']));
    $this->assertEquals('[entries_fetch id="foo" field="title"]',
                        flextype('parsers')->shortcode()->process('[raw][entries_fetch id="foo" field="title"][/raw]'));
});
