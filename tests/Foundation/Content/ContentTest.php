<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test create() method', function () {
    $this->assertTrue(flextype('content')->create('foo', []));
    $this->assertFalse(flextype('content')->create('foo', []));
});

test('test has()', function () {
    flextype('content')->create('foo', []);

    $this->assertTrue(flextype('content')->has('foo'));
    $this->assertFalse(flextype('content')->has('bar'));
});

test('test update() method', function () {
    flextype('content')->create('foo', []);

    $this->assertTrue(flextype('content')->update('foo', ['title' => 'Test']));
    $this->assertFalse(flextype('content')->update('bar', ['title' => 'Test']));
});

test('test fetch() entry', function () {
    flextype('content')->create('foo', ['title' => 'Foo']);
    flextype('content')->create('foo/bar', ['title' => 'Bar']);
    flextype('content')->create('foo/baz', ['title' => 'Baz']);
    flextype('content')->create('foo/zed', ['title' => 'Zed']);

    $this->assertEquals(12, flextype('content')->fetch('foo')->count());
    $this->assertEquals('foo', flextype('content')->fetch('foo')['id']);
    $this->assertEquals(12, flextype('content')->fetch('foo', [])->count());
    $this->assertEquals('foo', flextype('content')->fetch('foo')['id']);
    $this->assertEquals(3, flextype('content')->fetch('foo', ['collection' => true])->count());

    $this->assertEquals('Bar', flextype('content')->fetch('foo/bar')['title']);
    $this->assertEquals('Baz', flextype('content')->fetch('foo/baz')['title']);
    $this->assertEquals('Zed', flextype('content')->fetch('foo/zed')['title']);

    flextype('content')->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, flextype('content')->fetch('wrong-entry')->count());
    flextype('content')->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, flextype('content')->fetch('wrong-entry')->count());

    $this->assertTrue(count(flextype('content')->fetch('foo', ['collection' => true])) > 0);

/*
    flextype('emitter')->addListener('onContentFetchCollectionHasResult', static function (): void {
        flextype('content')->storage()->set('fetch_collection.data.foo/zed.title', 'ZedFromCollection!');
    });

    flextype('emitter')->addListener('onContentFetchCollectionHasResult', static function (): void {
        flextype('content')->storage()->set('fetch_collection.data.foo/baz.title', 'BazFromCollection!');
    });

    $this->assertEquals('ZedFromCollection!', flextype('content')->fetch('foo', ['collection' => true])['foo/zed.title']);
    $this->assertEquals('BazFromCollection!', flextype('content')->fetch('foo', ['collection' => true])['foo/baz.title']);
*/

});

test('test copy() method', function () {
    flextype('content')->create('foo', []);
    flextype('content')->create('foo/bar', []);
    flextype('content')->create('foo/baz', []);

    flextype('content')->create('zed', []);
    flextype('content')->copy('foo', 'zed');

    $this->assertTrue(flextype('content')->has('zed'));
});

test('test delete() method', function () {
    flextype('content')->create('foo', []);
    flextype('content')->create('foo/bar', []);
    flextype('content')->create('foo/baz', []);

    $this->assertTrue(flextype('content')->delete('foo'));
    $this->assertFalse(flextype('content')->has('foo'));
});

test('test move() method', function () {
    flextype('content')->create('foo', []);
    flextype('content')->create('zed', []);

    $this->assertTrue(flextype('content')->move('foo', 'bar'));
    $this->assertTrue(flextype('content')->has('bar'));
    $this->assertFalse(flextype('content')->has('foo'));
    $this->assertFalse(flextype('content')->move('zed', 'bar'));
});

test('test getFileLocation() method', function () {
    flextype('content')->create('foo', []);

    $this->assertStringContainsString('/foo/entry.md',
                          flextype('content')->getFileLocation('foo'));
});

test('test getDirectoryLocation() entry', function () {
    flextype('content')->create('foo', []);

    $this->assertStringContainsString('/foo',
                          flextype('content')->getDirectoryLocation('foo'));
});

test('test getCacheID() entry', function () {
    flextype('registry')->set('flextype.settings.cache.enabled', false);
    flextype('content')->create('foo', []);
    $this->assertEquals('', flextype('content')->getCacheID('foo'));

    flextype('registry')->set('flextype.settings.cache.enabled', true);
    flextype('content')->create('bar', []);
    $cache_id = flextype('content')->getCacheID('bar');
    $this->assertEquals(32, strlen($cache_id));
    flextype('registry')->set('flextype.settings.cache.enabled', false);
});

test('test storage() entry', function () {
    flextype('content')->storage()->set('foo', ['title' => 'Foo']);
    $this->assertEquals('Foo', flextype('content')->storage()->get('foo')['title']);
    flextype('content')->storage()->set('bar', ['title' => 'Bar']);
    $this->assertEquals(true, flextype('content')->storage()->has('foo.title'));
    $this->assertEquals(true, flextype('content')->storage()->has('bar.title'));
    flextype('content')->storage()->delete('foo.title');
    flextype('content')->storage()->delete('bar.title');
    $this->assertEquals(false, flextype('content')->storage()->has('foo.title'));
    $this->assertEquals(false, flextype('content')->storage()->has('bar.title'));
});

test('test macro() entry', function () {
    flextype('content')->create('foo', []);
    flextype('content')->create('foo/bar', []);
    flextype('content')->create('foo/baz', []);

    flextype('content')::macro('fetchRecentPosts', function($limit = 1) {
    	return flextype('content')
                    ->fetch('foo')
                    ->sortBy('published_at')
                    ->limit($limit);
    });

    $this->assertEquals(1, flextype('content')->fetchRecentPosts()->count());
    $this->assertEquals(1, flextype('content')->fetchRecentPosts(1)->count());
    $this->assertEquals(2, flextype('content')->fetchRecentPosts(2)->count());
});

test('test mixin() entry', function () {
    flextype('content')->create('foo', []);
    flextype('content')->create('foo/bar', []);
    flextype('content')->create('foo/baz', []);

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

    flextype('content')::mixin(new FooMixin());

    $this->assertEquals('Foo', flextype('content')->foo());
    $this->assertEquals('Foo', flextype('content')->bar());
    $this->assertEquals('Bar', flextype('content')->bar('Bar'));
});
