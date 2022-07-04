<?php

use Flextype\Component\Filesystem\Filesystem;

use function Glowy\Filesystem\filesystem;
use Respect\Validation\Validator as v;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('UuidField', function () {
    entries()->create('foo', []);
    $uuid = entries()->fetch('foo')['uuid'];
    $this->assertTrue(v::uuid()->validate($uuid));
});
