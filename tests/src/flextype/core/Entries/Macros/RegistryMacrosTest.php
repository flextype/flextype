<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('RegistryMacros', function () {
    entries()->create('registry-root', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/registry-root/entry.yaml')->get()));
    entries()->create('registry-root/level-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/registry-root/level-1/entry.yaml')->get()));
    entries()->create('registry-root/level-1/level-2', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/entries/registry-root/level-1/level-2/entry.yaml')->get()));

    $data = entries()->fetch('registry-root');

    $this->assertEquals('Flextype', $data['flextype']);
    $this->assertEquals('Sergey Romanenko', $data['author']['name']);
    $this->assertEquals('MIT', $data['license']);
    $this->assertEquals('Flextype', $data['level1']['flextype']);
    $this->assertEquals('Sergey Romanenko', $data['level1']['author']['name']);
    $this->assertEquals('MIT', $data['level1']['license']);
    $this->assertEquals('Flextype', $data['level1']['level2']['flextype']);
    $this->assertEquals('Sergey Romanenko', $data['level1']['level2']['author']['name']);
    $this->assertEquals('MIT', $data['level1']['level2']['license']);
});
