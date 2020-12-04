<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test create() method', function () {
    $this->assertTrue(flextype('entries')->create('foo', []));
    $this->assertFalse(flextype('entries')->create('foo', []));
});

test('test has()', function () {
    flextype('entries')->create('foo', []);

    $this->assertTrue(flextype('entries')->has('foo'));
    $this->assertFalse(flextype('entries')->has('bar'));
});

test('test update() method', function () {
    flextype('entries')->create('foo', []);

    $this->assertTrue(flextype('entries')->update('foo', ['title' => 'Test']));
    $this->assertFalse(flextype('entries')->update('bar', ['title' => 'Test']));
});


test('test fetchSingle() method', function () {
    // 1
    flextype('entries')->create('foo', []);
    $fetch = flextype('entries')->fetchSingle('foo');
    $this->assertTrue(count($fetch) > 0);

    // 2
    $this->assertEquals('foo', flextype('entries')->fetchSingle('foo')['id']);

    // 3
    flextype('entries')->create('zed', ['title' => 'Zed']);
    $fetch = flextype('entries')->fetchSingle('zed');
    $this->assertEquals('Zed', $fetch['title']);

    // 4
    flextype('entries')->setStorage('fetch.id', 'wrong-entry');
    $this->assertEquals(0, flextype('entries')->fetchSingle('wrong-entry')->count());
});

test('test fetchCollection() method', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', ['title' => 'Bar']);
    flextype('entries')->create('foo/baz', ['title' => 'Baz']);
    $fetch = flextype('entries')->fetchCollection('foo');
    $this->assertTrue(count($fetch) > 0);
});

test('test copy() method', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

    flextype('entries')->create('zed', []);
    flextype('entries')->copy('foo', 'zed');

    $this->assertTrue(flextype('entries')->has('zed'));
});

test('test delete() method', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

    $this->assertTrue(flextype('entries')->delete('foo'));
    $this->assertFalse(flextype('entries')->has('foo'));
});

test('test move() method', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('zed', []);

    $this->assertTrue(flextype('entries')->move('foo', 'bar'));
    $this->assertTrue(flextype('entries')->has('bar'));
    $this->assertFalse(flextype('entries')->has('foo'));
    $this->assertFalse(flextype('entries')->move('zed', 'bar'));
});

test('test getFileLocation() method', function () {
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
    $this->assertEquals('Foo', flextype('entries')->getStorage('foo.title'));
    $this->assertEquals('Bar', flextype('entries')->getStorage('bar.title'));
});
