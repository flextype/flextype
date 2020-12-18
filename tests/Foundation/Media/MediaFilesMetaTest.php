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

test('test update() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->meta()->update('foo.txt', 'description', 'Foo description'));
    $this->assertEquals('Foo description', flextype('serializers')->yaml()->decode(filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->get())['description']);
});

test('test add() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->meta()->add('foo.txt', 'bar', 'Bar'));
    $this->assertEquals('Bar', flextype('serializers')->yaml()->decode(filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->get())['bar']);
});

test('test delete() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media')->files()->meta()->delete('foo.txt', 'title'));
    $this->assertTrue(empty(flextype('serializers')->yaml()->decode(filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->get())['bar']));
});

test('test getFileMetaLocation() method', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(flextype('serializers')->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    $this->assertStringContainsString('foo.txt.yaml',
                          flextype('media')->files()->meta()->getFileMetaLocation('foo.txt'));
});
