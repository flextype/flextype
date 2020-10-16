<?php

use Flextype\Component\Filesystem\Filesystem;
use Atomastic\Strings\Strings;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test create single entry', function () {
    $this->assertTrue(flextype('entries')->create('foo', []));
    $this->assertFalse(flextype('entries')->create('foo', []));
});

test('test has single entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertTrue(flextype('entries')->has('foo'));
    $this->assertFalse(flextype('entries')->has('bar'));
});

test('test update single entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertTrue(flextype('entries')->update('foo', ['title' => 'Test']));
    $this->assertFalse(flextype('entries')->update('bar', ['title' => 'Test']));
});

test('test fetch single entry', function () {
    // 1
    flextype('entries')->create('foo', []);
    $fetch = flextype('entries')->fetch('foo');
    $this->assertTrue(count($fetch) > 0);

    // 2
    $this->assertEquals([], flextype('entries')->fetch('bar'));

    // 3
    flextype('entries')->create('zed', ['title' => 'Zed']);
    $fetch = flextype('entries')->fetch('zed');
    $this->assertEquals('Zed', $fetch['title']);
});

test('test fetch collection entry', function () {
    // 1
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);
    $fetch = flextype('entries')->fetchCollection('foo');
    $this->assertTrue(count($fetch) > 0);
});
