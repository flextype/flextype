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

test('test update() method', function () {
    flextype('filesystem')->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    $this->assertTrue(flextype('media_files_meta')->update('foo.txt', 'description', 'Foo description'));
    $this->assertEquals('Foo description', flextype('yaml')->decode(flextype('filesystem')->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->get())['description']);
});
