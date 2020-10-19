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

test('test fetch() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    flextype('filesystem')->file(PATH['project'] . '/uploads/bar.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/bar.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Bar', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(count(flextype('media_files')->fetch('foo.txt')) > 0);
    $this->assertEquals('Foo', flextype('media_files')->fetch('foo.txt')['title']);
    $this->assertTrue(count(flextype('media_files')->fetch('/', true)) == 2);
    $this->assertEquals('Foo', flextype('media_files')->fetch('/', true)['foo.txt']['title']);
});

test('test fetchSingle() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(count(flextype('media_files')->fetchSingle('foo.txt')) > 0);
    $this->assertEquals('Foo', flextype('media_files')->fetchSingle('foo.txt')['title']);
});

test('test fetchCollection() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    flextype('filesystem')->file(PATH['project'] . '/uploads/bar.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/bar.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Bar', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(count(flextype('media_files')->fetchCollection('/')) == 2);
    $this->assertEquals('Foo', flextype('media_files')->fetchCollection('/')['foo.txt']['title']);
});

test('test move() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media_files')->move('foo.txt', 'bar.txt'));
    $this->assertTrue(flextype('media_files')->move('bar.txt', 'foo.txt'));
    $this->assertFalse(flextype('media_files')->move('bar.txt', 'foo.txt'));
});

test('test copy() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
    $this->assertTrue(flextype('media_folders')->create('bar'));

    flextype('filesystem')->file(PATH['project'] . '/uploads/foo/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media_files')->copy('foo/foo.txt', 'bar/foo.txt'));
    $this->assertTrue(flextype('media_files')->copy('foo/foo.txt', 'bar/bar.txt'));
    $this->assertFalse(flextype('media_files')->copy('foo/foo.txt', 'bar/foo.txt'));
});

test('test has() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media_files')->has('foo.txt'));
    $this->assertFalse(flextype('media_files')->has('bar.txt'));
});
