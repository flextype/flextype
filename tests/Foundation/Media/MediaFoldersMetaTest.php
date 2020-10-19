<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/uploads')->create();
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/uploads/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
});

test('test getDirectoryMetaLocation() method', function () {
    $this->assertStringContainsString('/.meta/foo',
                          flextype('media_folders_meta')->getDirectoryMetaLocation('foo'));
});
