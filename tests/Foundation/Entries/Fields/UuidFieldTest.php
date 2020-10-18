<?php

use Flextype\Component\Filesystem\Filesystem;

use Respect\Validation\Validator as v;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test UuidField', function () {
    flextype('entries')->create('foo', []);
    $uuid = flextype('entries')->fetch('foo')['uuid'];
    $this->assertTrue(v::uuid()->validate($uuid));
});
