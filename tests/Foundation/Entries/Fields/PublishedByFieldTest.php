<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test PublishedByField', function () {
    flextype('content')->create('foo', []);
    $published_by = flextype('content')->fetch('foo')['published_by'];
    $this->assertEquals('', $published_by);

    flextype('content')->create('bar', ['published_by' => 'Zed']);
    $published_by = flextype('content')->fetch('bar')['published_by'];
    $this->assertEquals('Zed', $published_by);
});
