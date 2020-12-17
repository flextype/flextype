<?php

declare(strict_types=1);

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/uploads')->create(0755, true);
    filesystem()->directory(PATH['project'] . '/media/.meta')->create(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/media/.meta')->delete();
    filesystem()->directory(PATH['project'] . '/uploads')->delete();
});

test('test getDirectoryMetaLocation() method', function () {
    $this->assertStringContainsString('/.meta/foo',
                          flextype('media_folders_meta')->getDirectoryMetaLocation('foo'));
});
