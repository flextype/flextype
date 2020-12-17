<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test PublishedByField', function () {
    flextype('entries')->create('foo', []);
    $published_by = flextype('entries')->fetch('foo')['published_by'];
    $this->assertEquals('', $published_by);

    flextype('entries')->create('bar', ['published_by' => 'Zed']);
    $published_by = flextype('entries')->fetch('bar')['published_by'];
    $this->assertEquals('Zed', $published_by);
});
