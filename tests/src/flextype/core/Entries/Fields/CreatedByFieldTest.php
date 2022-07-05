<?php

use Flextype\Component\Filesystem\Filesystem;

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('CreatedByField', function () {
    entries()->create('foo', []);
    $created_by = entries()->fetch('foo')['created_by'];
    $this->assertEquals('', $created_by);

    entries()->create('bar', ['created_by' => 'Zed']);
    $created_by = entries()->fetch('bar')['created_by'];
    $this->assertEquals('Zed', $created_by);
});
