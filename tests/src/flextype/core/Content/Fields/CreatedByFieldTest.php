<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test CreatedByField', function () {
    content()->create('foo', []);
    $created_by = content()->fetch('foo')['created_by'];
    $this->assertEquals('', $created_by);

    content()->create('bar', ['created_by' => 'Zed']);
    $created_by = content()->fetch('bar')['created_by'];
    $this->assertEquals('Zed', $created_by);
});
