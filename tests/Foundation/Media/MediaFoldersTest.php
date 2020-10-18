<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/uploads')->create();
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
});

test('test create() method', function () {
    $this->assertTrue(flextype('media_folders')->create('foo'));
});
