<?php

use Flextype\Component\Filesystem\Filesystem;

use Respect\Validation\Validator as v;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test UuidField', function () {
    content()->create('foo', []);
    $uuid = content()->fetch('foo')['uuid'];
    $this->assertTrue(v::uuid()->validate($uuid));
});
