<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/uploads')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
});


test('test fetch() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
    $this->assertTrue(flextype('media_folders')->create('foo/bar'));
    $this->assertTrue(flextype('media_folders')->create('foo/zed'));
    $this->assertTrue(count(flextype('media_folders')->fetch('foo', ['collection' => true])) == 2);
    $this->assertTrue(count(flextype('media_folders')->fetch('foo')) > 0);
});

test('test create() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
});

test('test move() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
    $this->assertTrue(flextype('media_folders')->move('foo', 'bar'));
});

test('test copy() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
    $this->assertTrue(flextype('media_folders')->copy('foo', 'bar'));
});

test('test delete() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
    $this->assertTrue(flextype('media_folders')->delete('foo'));
    $this->assertFalse(flextype('media_folders')->delete('bar'));
});

test('test getDirectoryLocation() method', function () {
    $this->assertStringContainsString('/foo',
                          flextype('media_folders')->getDirectoryLocation('foo'));
});
