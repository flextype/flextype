<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test entries_fetch  shortcode', function () {
    $this->assertTrue(flextype('entries')->create('foo', ['title' => 'Foo']));
    $this->assertEquals('Foo', flextype('parsers')->shortcode()->process('[entries_fetch id="foo" field="title"]'));
    $this->assertEquals('Bar', flextype('parsers')->shortcode()->process('[entries_fetch id="foo" field="bar" default="Bar"]'));
});
