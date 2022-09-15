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

test('ModifiedAtField', function () {
    entries()->create('foo', []);

    $modified_at = entries()->fetch('foo')['modified_at'];

    $this->assertTrue(strlen($modified_at) > 0);
    $this->assertTrue((strtotime(date('Y-m-d H:i:s', $modified_at)) === (int)$modified_at));
});
