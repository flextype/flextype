<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test PublishedByField', function () {
    content()->create('foo', []);
    $published_by = content()->fetch('foo')['published_by'];
    $this->assertEquals('', $published_by);

    content()->create('bar', ['published_by' => 'Zed']);
    $published_by = content()->fetch('bar')['published_by'];
    $this->assertEquals('Zed', $published_by);
});
