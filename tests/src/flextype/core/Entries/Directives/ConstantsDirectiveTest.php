<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('constants directive', function () {
    entries()->create('const-root-dir', ['path' => '@const[ROOT_DIR]']);
    entries()->create('const-root-dir-2', ['path' => '@const[PATH_PROJECT]']);

    $this->assertEquals(ROOT_DIR, entries()->fetch('const-root-dir')['path']);
    $this->assertEquals(PATH['project'], entries()->fetch('const-root-dir-2')['path']);
});

test('constants directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.constants.enabled', false);
    entries()->create('const-root-dir-3', ['path' => '@const[ROOT_DIR]']);
    expect(entries()->fetch('const-root-dir-3')['path'])->toBe('@const[ROOT_DIR]');
    registry()->set('flextype.settings.entries.directives.constants.enabled', true);
});