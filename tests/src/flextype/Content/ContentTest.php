<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->ensureExists(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test create() method', function () {
    $this->assertTrue(content()->create('foo', []));
    $this->assertFalse(content()->create('foo', []));
});

test('test has()', function () {
    content()->create('foo', []);

    $this->assertTrue(content()->has('foo'));
    $this->assertFalse(content()->has('bar'));
});

test('test update() method', function () {
    content()->create('foo', []);

    $this->assertTrue(content()->update('foo', ['title' => 'Test']));
    $this->assertFalse(content()->update('bar', ['title' => 'Test']));
});

test('test fetch() content', function () {
    content()->create('foo', ['title' => 'Foo']);
    content()->create('foo/bar', ['title' => 'Bar']);
    content()->create('foo/baz', ['title' => 'Baz']);
    content()->create('foo/zed', ['title' => 'Zed']);

    $this->assertEquals(11, content()->fetch('foo')->count());
    $this->assertEquals('foo', content()->fetch('foo')['id']);
    $this->assertEquals(11, content()->fetch('foo', [])->count());
    $this->assertEquals('foo', content()->fetch('foo')['id']);
    $this->assertEquals(3, content()->fetch('foo', ['collection' => true])->count());

    $this->assertEquals('Bar', content()->fetch('foo/bar')['title']);
    $this->assertEquals('Baz', content()->fetch('foo/baz')['title']);
    $this->assertEquals('Zed', content()->fetch('foo/zed')['title']);

    content()->registry()->set('fetch.id', 'wrong-content');
    $this->assertEquals(0, content()->fetch('wrong-content')->count());
    content()->registry()->set('fetch.id', 'wrong-content');
    $this->assertEquals(0, content()->fetch('wrong-content')->count());

    $this->assertTrue(count(content()->fetch('foo', ['collection' => true])) > 0);

    $this->assertEquals(['title' => 'Foo'], content()->fetch('foo', ['filter' => ['only' => ['title']]])->toArray());
    $this->assertEquals(10, content()->fetch('foo', ['filter' => ['except' => ['title']]])->count());
    $this->assertEquals(1, content()->fetch('foo', ['filter' => ['only' => ['title']]])->count());
    $this->assertEquals(['foo/zed' => ['title' => 'Zed'], 'foo/baz' => ['title' => 'Baz'], 'foo/bar' => ['title' => 'Bar']], content()->fetch('foo', ['collection' => true, 'filter' => ['only' => ['title']]])->toArray());
    $except = content()->fetch('foo', ['collection' => true, 'filter' => ['except' => ['title']]]);
    $this->assertEquals(10, count($except['foo/bar']));
});

test('test copy() method', function () {
    content()->create('foo', []);
    content()->create('foo/bar', []);
    content()->create('foo/baz', []);

    content()->create('zed', []);
    content()->copy('foo', 'zed');

    $this->assertTrue(content()->has('zed'));
});

test('test delete() method', function () {
    content()->create('foo', []);
    content()->create('foo/bar', []);
    content()->create('foo/baz', []);

    $this->assertTrue(content()->delete('foo'));
    $this->assertFalse(content()->has('foo'));
});

test('test move() method', function () {
    content()->create('foo', []);
    content()->create('zed', []);

    $this->assertTrue(content()->move('foo', 'bar'));
    $this->assertTrue(content()->has('bar'));
    $this->assertFalse(content()->has('foo'));
    $this->assertFalse(content()->move('zed', 'bar'));
});

test('test getFileLocation() method', function () {
    content()->create('foo', []);

    $this->assertStringContainsString('/foo/content.yaml',
                          content()->getFileLocation('foo'));
});

test('test getDirectoryLocation() content', function () {
    content()->create('foo', []);

    $this->assertStringContainsString('/foo',
                          content()->getDirectoryLocation('foo'));
});

test('test getCacheID() content', function () {
    registry()->set('flextype.settings.cache.enabled', false);
    content()->create('foo', []);
    $this->assertEquals('', content()->getCacheID('foo'));

    registry()->set('flextype.settings.cache.enabled', true);
    content()->create('bar', []);
    $cache_id = content()->getCacheID('bar');
    $this->assertEquals(32, strlen($cache_id));
    registry()->set('flextype.settings.cache.enabled', false);
});

test('test storage() content', function () {
    content()->registry()->set('foo', ['title' => 'Foo']);
    $this->assertEquals('Foo', content()->registry()->get('foo')['title']);
    content()->registry()->set('bar', ['title' => 'Bar']);
    $this->assertEquals(true, content()->registry()->has('foo.title'));
    $this->assertEquals(true, content()->registry()->has('bar.title'));
    content()->registry()->delete('foo.title');
    content()->registry()->delete('bar.title');
    $this->assertEquals(false, content()->registry()->has('foo.title'));
    $this->assertEquals(false, content()->registry()->has('bar.title'));
});

test('test macro() content', function () {
    content()->create('foo', []);
    content()->create('foo/bar', []);
    content()->create('foo/baz', []);

    content()::macro('fetchRecentPosts', function($limit = 1) {
    	return content()
                    ->fetch('foo')
                    ->sortBy('published_at')
                    ->limit($limit);
    });

    $this->assertEquals(1, content()->fetchRecentPosts()->count());
    $this->assertEquals(1, content()->fetchRecentPosts(1)->count());
    $this->assertEquals(2, content()->fetchRecentPosts(2)->count());
});

test('test mixin() content', function () {
    content()->create('foo', []);
    content()->create('foo/bar', []);
    content()->create('foo/baz', []);

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

    content()::mixin(new FooMixin());

    $this->assertEquals('Foo', content()->foo());
    $this->assertEquals('Foo', content()->bar());
    $this->assertEquals('Bar', content()->bar('Bar'));
});
