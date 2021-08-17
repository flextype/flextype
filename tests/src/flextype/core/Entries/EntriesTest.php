<?php

use Atomastic\Arrays\Arrays;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('create new entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->create('foo', []))->toBeFalse();
});

test('has entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->has('foo'))->toBeTrue();
    expect(entries()->has('bar'))->toBeFalse();
});

test('update entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->update('foo', ['title' => 'Foo']))->toBeTrue();
    expect(entries()->update('bar', ['title' => 'Bar']))->toBeFalse();
});

test('delete entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->create('foo/bar', []))->toBeTrue();
    expect(entries()->create('foo/zed', []))->toBeTrue();

    expect(entries()->delete('foo'))->toBeTrue();
    expect(entries()->has('foo'))->toBeFalse();
});

test('move entry foo into bar', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->create('bar', []))->toBeTrue();

    expect(entries()->move('foo', 'bar/foo'))->toBeTrue();
    expect(entries()->has('bar/foo'))->toBeTrue();
});

test('copy entry foo into bar', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->create('bar', []))->toBeTrue();

    expect(entries()->copy('foo', 'bar/foo'))->toBeTrue();
    expect(entries()->has('bar/foo'))->toBeTrue();
    expect(entries()->has('foo'))->toBeTrue();
});

test('fetch entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();
    
    expect(entries()->fetch('foo'))->toBeInstanceOf(Arrays::class);
    expect(count(entries()->fetch('foo')->toArray()) > 0)->toBeTrue();
});

test('get file location for entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();

    expect(entries()->getFileLocation('foo'))->toContain('/foo/entry.yaml');
});

test('get directory location for entry foo', function () {
    expect(entries()->create('foo', []))->toBeTrue();

    expect(entries()->getDirectoryLocation('foo'))->toContain('/foo');
});

test('get cache ID for entry foo with cache enabled false', function () {
    registry()->set('flextype.settings.cache.enabled', false);
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->getCacheID('foo'))->toBeEmpty();
});

test('get cache ID for entry foo with cache enabled true', function () {
    registry()->set('flextype.settings.cache.enabled', true);
    expect(entries()->create('foo', []))->toBeTrue();
    expect(strlen(entries()->getCacheID('foo')))->toEqual(32);
});

test('registry for entry foo', function() {
    entries()->registry()->set('foo', ['title' => 'Foo']);
    expect(entries()->registry()->get('foo.title'))->toEqual('Foo');
    entries()->registry()->set('bar', ['title' => 'Bar']);
    expect(entries()->registry()->get('bar.title'))->toEqual('Bar');

    expect(entries()->registry()->has('foo.title'))->toBeTrue();
    expect(entries()->registry()->has('bar.title'))->toBeTrue();

    entries()->registry()->delete('foo.title');
    entries()->registry()->delete('bar.title');

    expect(entries()->registry()->has('foo.title'))->toBeFalse();
    expect(entries()->registry()->has('bar.title'))->toBeFalse();
});

test('macro for entries', function() {
    expect(entries()->create('foo', []))->toBeTrue();
    expect(entries()->create('foo/bar', []))->toBeTrue();
    expect(entries()->create('foo/zed', []))->toBeTrue();

    entries()::macro('fetchRecentEntries', function(int $limit = 1) {
        return entries()
                ->fetch('foo', ['collection' => true])
                ->limit($limit);
    });

    expect(entries()->fetchRecentEntries()->count())->toEqual(1);
    expect(entries()->fetchRecentEntries(1)->count())->toEqual(1);
    expect(entries()->fetchRecentEntries(2)->count())->toEqual(2);
});

test('mixin for entries', function() {
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

    expect(entries()->foo())->toEqual('Foo');
    expect(entries()->bar())->toEqual('Foo');
    expect(entries()->bar('Bar'))->toEqual('Bar');
});

test('get entries options', function () {
    expect(entries()->getOptions())->toBeArray();
});