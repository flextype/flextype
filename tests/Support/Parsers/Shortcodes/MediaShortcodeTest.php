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

test('test media_files_fetch shortcode', function () {
    filesystem()->file(PATH['project'] . '/media/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/foo.txt.yaml')->put(serializers()->yaml()->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    filesystem()->file(PATH['project'] . '/media/bar.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/media/.meta/bar.txt.yaml')->put(serializers()->yaml()->encode(['title' => 'Bar', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertEquals('Foo', parsers()->shortcode()->process('[media_files_fetch id="foo.txt" field="title"]'));
    $this->assertEquals('Bar', parsers()->shortcode()->process('[media_files_fetch id="foo.txt" field="foo" default="Bar"]'));
});
