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
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    filesystem()->file(PATH['project'] . '/media/bar.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/bar.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Bar', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(count(flextype('media')->files()->fetch('foo.txt')) > 0);
    $this->assertEquals('Foo', flextype('media')->files()->fetch('foo.txt')['title']);

    $this->assertTrue(count(flextype('media')->files()->fetch('/', ['collection' => true])) == 2);
    $this->assertEquals('Foo', flextype('media')->files()->fetch('/', ['collection' => true])['foo.txt']['title']);
});

test('test move() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->move('foo.txt', 'bar.txt'));
    $this->assertTrue(flextype('media')->files()->move('bar.txt', 'foo.txt'));
    $this->assertFalse(flextype('media')->files()->move('bar.txt', 'foo.txt'));
});

test('test copy() method', function () {
    $this->assertTrue(flextype('media')->folders()->create('foo'));
    $this->assertTrue(flextype('media')->folders()->create('bar'));

    filesystem()->file(PATH['project'] . '/media/foo/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->copy('foo/foo.txt', 'bar/foo.txt'));
    $this->assertTrue(flextype('media')->files()->copy('foo/foo.txt', 'bar/bar.txt'));
    $this->assertFalse(flextype('media')->files()->copy('foo/foo.txt', 'bar/foo.txt'));
});

test('test has() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->has('foo.txt'));
    $this->assertFalse(flextype('media')->files()->has('bar.txt'));
});

test('test getFileLocation() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    $this->assertStringContainsString('foo.txt', flextype('media')->files()->getFileLocation('foo.txt'));
});

test('test delete() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    $this->assertTrue(flextype('media')->files()->delete('foo.txt'));
    $this->assertFalse(flextype('media')->files()->delete('foo.txt'));
});
