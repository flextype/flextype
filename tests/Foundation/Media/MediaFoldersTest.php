<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/media')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/media/.meta')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/media/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/media')->delete();
});


test('test fetch() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->create('foo/bar'));
    $this->assertTrue(flextype('media')->folders()->create('foo/zed'));
    $this->assertTrue(count(flextype('media')->folders()->fetch('foo', ['collection' => true])) == 2);
    $this->assertTrue(count(flextype('media')->folders()->fetch('foo')) > 0);
});

test('test create() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
});

test('test move() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->move('foo', 'bar'));
});

test('test copy() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->copy('foo', 'bar'));
});


test('test has() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->has('foo'));
    $this->assertFalse(flextype('media')->folders()->has('bar'));
});

test('test delete() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->delete('foo'));
    $this->assertFalse(flextype('media')->folders()->delete('bar'));
});

test('test getDirectoryLocation() method', function () {
    $this->assertStringContainsString('/foo',
                          flextype('media')->folders()->getDirectoryLocation('foo'));
});
