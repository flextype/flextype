<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test create() method', function () {
    $this->assertTrue(entries()->create('foo', []));
    $this->assertFalse(entries()->create('foo', []));
});

test('test has()', function () {
    entries()->create('foo', []);

    $this->assertTrue(entries()->has('foo'));
    $this->assertFalse(entries()->has('bar'));
});

test('test update() method', function () {
    entries()->create('foo', []);

    $this->assertTrue(entries()->update('foo', ['title' => 'Test']));
    $this->assertFalse(entries()->update('bar', ['title' => 'Test']));
});

test('test fetch() entry', function () {
    entries()->create('foo', ['title' => 'Foo']);
    entries()->create('foo/bar', ['title' => 'Bar']);
    entries()->create('foo/baz', ['title' => 'Baz']);
    entries()->create('foo/zed', ['title' => 'Zed']);

    $this->assertEquals(12, entries()->fetch('foo')->count());
    $this->assertEquals('foo', entries()->fetch('foo')['id']);
    $this->assertEquals(12, entries()->fetch('foo', [])->count());
    $this->assertEquals('foo', entries()->fetch('foo')['id']);
    $this->assertEquals(3, entries()->fetch('foo', ['collection' => true])->count());

    $this->assertEquals('Bar', entries()->fetch('foo/bar')['title']);
    $this->assertEquals('Baz', entries()->fetch('foo/baz')['title']);
    $this->assertEquals('Zed', entries()->fetch('foo/zed')['title']);

    entries()->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, entries()->fetch('wrong-entry')->count());
    entries()->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, entries()->fetch('wrong-entry')->count());

    $this->assertTrue(count(entries()->fetch('foo', ['collection' => true])) > 0);
});

test('test copy() method', function () {
    entries()->create('foo', []);
    entries()->create('foo/bar', []);
    entries()->create('foo/baz', []);

    entries()->create('zed', []);
    entries()->copy('foo', 'zed');

    $this->assertTrue(entries()->has('zed'));
});

test('test delete() method', function () {
    entries()->create('foo', []);
    entries()->create('foo/bar', []);
    entries()->create('foo/baz', []);

    $this->assertTrue(entries()->delete('foo'));
    $this->assertFalse(entries()->has('foo'));
});

test('test move() method', function () {
    entries()->create('foo', []);
    entries()->create('zed', []);

    $this->assertTrue(entries()->move('foo', 'bar'));
    $this->assertTrue(entries()->has('bar'));
    $this->assertFalse(entries()->has('foo'));
    $this->assertFalse(entries()->move('zed', 'bar'));
});

test('test getFileLocation() method', function () {
    entries()->create('foo', []);

    $this->assertStringContainsString('/foo/entry.yaml',
                          entries()->getFileLocation('foo'));
});

test('test getDirectoryLocation() entry', function () {
    entries()->create('foo', []);

    $this->assertStringContainsString('/foo',
                          entries()->getDirectoryLocation('foo'));
});

test('test getCacheID() entry', function () {
    registry()->set('flextype.settings.cache.enabled', false);
    entries()->create('foo', []);
    $this->assertEquals('', entries()->getCacheID('foo'));

    registry()->set('flextype.settings.cache.enabled', true);
    entries()->create('bar', []);
    $cache_id = entries()->getCacheID('bar');
    $this->assertEquals(32, strlen($cache_id));
    registry()->set('flextype.settings.cache.enabled', false);
});

test('test storage() entry', function () {
    entries()->storage()->set('foo', ['title' => 'Foo']);
    $this->assertEquals('Foo', entries()->storage()->get('foo')['title']);
    entries()->storage()->set('bar', ['title' => 'Bar']);
    $this->assertEquals(true, entries()->storage()->has('foo.title'));
    $this->assertEquals(true, entries()->storage()->has('bar.title'));
    entries()->storage()->delete('foo.title');
    entries()->storage()->delete('bar.title');
    $this->assertEquals(false, entries()->storage()->has('foo.title'));
    $this->assertEquals(false, entries()->storage()->has('bar.title'));
});

test('test macro() entry', function () {
    entries()->create('foo', []);
    entries()->create('foo/bar', []);
    entries()->create('foo/baz', []);

    entries()::macro('fetchRecentPosts', function($limit = 1) {
    	return entries()
                    ->fetch('foo')
                    ->sortBy('published_at')
                    ->limit($limit);
    });

    $this->assertEquals(1, entries()->fetchRecentPosts()->count());
    $this->assertEquals(1, entries()->fetchRecentPosts(1)->count());
    $this->assertEquals(2, entries()->fetchRecentPosts(2)->count());
});

test('test mixin() entry', function () {
    entries()->create('foo', []);
    entries()->create('foo/bar', []);
    entries()->create('foo/baz', []);

    class FooMixin {
        public function foo() {
            return function () {
                return 'Foo';
            };
        }

        public function bar() {
            return function ($val = 'Foo') {
                return $val;
            };
        }
    }

    entries()::mixin(new FooMixin());

    $this->assertEquals('Foo', entries()->foo());
    $this->assertEquals('Foo', entries()->bar());
    $this->assertEquals('Bar', entries()->bar('Bar'));
});
