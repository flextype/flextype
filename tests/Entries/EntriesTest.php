<?php

use Flextype\Component\Filesystem\Filesystem;

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
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);
    $fetch = flextype('entries')->fetchCollection('foo');
    $this->assertTrue(count($fetch) > 0);
});

test('test copy entry', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

    flextype('entries')->create('zed', []);
    flextype('entries')->copy('foo', 'zed');

    $this->assertTrue(flextype('entries')->has('zed'));
});

test('test delete entry', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

    $this->assertTrue(flextype('entries')->delete('foo'));
    $this->assertFalse(flextype('entries')->has('foo'));
});

test('test move entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertTrue(flextype('entries')->move('foo', 'bar'));
    $this->assertTrue(flextype('entries')->has('bar'));
    $this->assertFalse(flextype('entries')->has('foo'));
});

test('test getFileLocation entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertStringContainsString('/foo/entry.md',
                          flextype('entries')->getFileLocation('foo'));
});

test('test getDirectoryLocation entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertStringContainsString('/foo',
                          flextype('entries')->getDirectoryLocation('foo'));
});

test('test getCacheID entry', function () {
    flextype('registry')->set('flextype.settings.cache.enabled', false);
    flextype('entries')->create('foo', []);
    $this->assertEquals('', flextype('entries')->getCacheID('foo'));

    flextype('registry')->set('flextype.settings.cache.enabled', true);
    flextype('entries')->create('bar', []);
    $cache_id = flextype('entries')->getCacheID('bar');
    $this->assertEquals(32, strlen($cache_id));
    flextype('registry')->set('flextype.settings.cache.enabled', false);
});

test('test setStorage and getStorage entry', function () {
    flextype('entries')->setStorage('foo', ['title' => 'Foo']);
    flextype('entries')->setStorage('bar', ['title' => 'Bar']);
    $this->assertEquals('Foo', flextype('entries')->getStorage('foo')['title']);
    $this->assertEquals('Bar', flextype('entries')->getStorage('bar')['title']);
});
