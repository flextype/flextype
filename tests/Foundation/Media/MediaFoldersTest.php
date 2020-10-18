<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/uploads')->create();
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
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
