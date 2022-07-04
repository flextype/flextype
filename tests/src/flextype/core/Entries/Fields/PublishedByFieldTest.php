<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('PublishedByField', function () {
    entries()->create('foo', []);
    $published_by = entries()->fetch('foo')['published_by'];
    $this->assertEquals('', $published_by);

    entries()->create('zedd', ['published_by' => 'Zed']);
    $published_by = entries()->fetch('zedd')['published_by'];
    
    $this->assertEquals('Zed', $published_by);
});
