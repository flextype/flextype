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

test('test fetch() entry', function () {
    flextype('entries')->create('foo', ['title' => 'Foo']);
    flextype('entries')->create('foo/bar', ['title' => 'Bar']);
    flextype('entries')->create('foo/baz', ['title' => 'Baz']);
    flextype('entries')->create('foo/zed', ['title' => 'Zed']);

    $this->assertEquals(12, flextype('entries')->fetch('foo')->count());
    $this->assertEquals('foo', flextype('entries')->fetch('foo')['id']);
    $this->assertEquals(12, flextype('entries')->fetch('foo', [])->count());
    $this->assertEquals('foo', flextype('entries')->fetch('foo')['id']);
    $this->assertEquals(3, flextype('entries')->fetch('foo', ['collection' => true])->count());

    $this->assertEquals('Bar', flextype('entries')->fetch('foo/bar')['title']);
    $this->assertEquals('Baz', flextype('entries')->fetch('foo/baz')['title']);
    $this->assertEquals('Zed', flextype('entries')->fetch('foo/zed')['title']);

    flextype('entries')->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, flextype('entries')->fetch('wrong-entry')->count());
    flextype('entries')->storage()->set('fetch.id', 'wrong-entry');
    $this->assertEquals(0, flextype('entries')->fetch('wrong-entry')->count());

    $this->assertTrue(count(flextype('entries')->fetch('foo', ['collection' => true])) > 0);

/*
    flextype('emitter')->addListener('onEntriesFetchCollectionHasResult', static function (): void {
        flextype('entries')->storage()->set('fetch_collection.data.foo/zed.title', 'ZedFromCollection!');
    });

    flextype('emitter')->addListener('onEntriesFetchCollectionHasResult', static function (): void {
        flextype('entries')->storage()->set('fetch_collection.data.foo/baz.title', 'BazFromCollection!');
    });

    $this->assertEquals('ZedFromCollection!', flextype('entries')->fetch('foo', ['collection' => true])['foo/zed.title']);
    $this->assertEquals('BazFromCollection!', flextype('entries')->fetch('foo', ['collection' => true])['foo/baz.title']);
*/

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

test('test getDirectoryLocation() entry', function () {
    flextype('entries')->create('foo', []);

    $this->assertStringContainsString('/foo',
                          flextype('entries')->getDirectoryLocation('foo'));
});

test('test getCacheID() entry', function () {
    flextype('registry')->set('flextype.settings.cache.enabled', false);
    flextype('entries')->create('foo', []);
    $this->assertEquals('', flextype('entries')->getCacheID('foo'));

    flextype('registry')->set('flextype.settings.cache.enabled', true);
    flextype('entries')->create('bar', []);
    $cache_id = flextype('entries')->getCacheID('bar');
    $this->assertEquals(32, strlen($cache_id));
    flextype('registry')->set('flextype.settings.cache.enabled', false);
});

test('test storage() entry', function () {
    flextype('entries')->storage()->set('foo', ['title' => 'Foo']);
    $this->assertEquals('Foo', flextype('entries')->storage()->get('foo')['title']);
    flextype('entries')->storage()->set('bar', ['title' => 'Bar']);
    $this->assertEquals(true, flextype('entries')->storage()->has('foo.title'));
    $this->assertEquals(true, flextype('entries')->storage()->has('bar.title'));
    flextype('entries')->storage()->delete('foo.title');
    flextype('entries')->storage()->delete('bar.title');
    $this->assertEquals(false, flextype('entries')->storage()->has('foo.title'));
    $this->assertEquals(false, flextype('entries')->storage()->has('bar.title'));
});

test('test macro() entry', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

    flextype('entries')::macro('fetchRecentPosts', function($limit = 1) {
    	return flextype('entries')
                    ->fetch('foo')
                    ->sortBy('published_at')
                    ->limit($limit);
    });

    $this->assertEquals(1, flextype('entries')->fetchRecentPosts()->count());
    $this->assertEquals(1, flextype('entries')->fetchRecentPosts(1)->count());
    $this->assertEquals(2, flextype('entries')->fetchRecentPosts(2)->count());
});

test('test mixin() entry', function () {
    flextype('entries')->create('foo', []);
    flextype('entries')->create('foo/bar', []);
    flextype('entries')->create('foo/baz', []);

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

    flextype('entries')::mixin(new FooMixin());

    $this->assertEquals('Foo', flextype('entries')->foo());
    $this->assertEquals('Foo', flextype('entries')->bar());
    $this->assertEquals('Bar', flextype('entries')->bar('Bar'));
});
