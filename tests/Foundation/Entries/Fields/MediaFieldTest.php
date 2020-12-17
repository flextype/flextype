<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
    filesystem()->directory(PATH['project'] . '/uploads')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/uploads/foo')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/uploads/.meta/foo')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test media_files field', function () {

    filesystem()->file(PATH['project'] . '/uploads/foo.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/uploads/.meta/foo.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Foo', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));
    filesystem()->file(PATH['project'] . '/uploads/bar.txt')->put('foo');
    filesystem()->file(PATH['project'] . '/uploads/.meta/bar.txt.yaml')->put(flextype('yaml')->encode(['title' => 'Bar', 'description' => '', 'type' => 'text/plain', 'filesize' => 3, 'uploaded_on' => 1603090370, 'exif' => []]));

    flextype('entries')->create('media', flextype('frontmatter')->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/media/entry.md')->get()));

    flextype('media_files')::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    flextype('media_folders')::macro('fetchExtraData', function ($id, $options) {
        return ['id' => $id, 'options' => $options];
    });

    $media = flextype('entries')->fetch('media');

    $this->assertEquals('Media', $media['title']);
    $this->assertEquals('foo', $media['macroable_file']['id']);
    $this->assertEquals('foo.txt', $media['foo_file']['filename']);
    $this->assertEquals(2, $media['collection_of_files']->count());

    $this->assertEquals('foo', $media['macroable_folder']['id']);
    $this->assertEquals(4, $media['foo_folder']->count());
    $this->assertEquals(1, $media['collection_of_folders']->count());
});
