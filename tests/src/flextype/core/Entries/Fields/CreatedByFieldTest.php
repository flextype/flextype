<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('CreatedByField', function () {
    entries()->create('foo', []);
    $created_by = entries()->fetch('foo')['created_by'];
    $this->assertEquals('', $created_by);

    entries()->create('bar', ['created_by' => 'Zed']);
    $created_by = entries()->fetch('bar')['created_by'];
    $this->assertEquals('Zed', $created_by);
});
