<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test registry field', function () {
    content()->create('registry-root', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/content/registry-root/content.yaml')->get()));
    content()->create('registry-root/level-1', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/content/registry-root/level-1/content.yaml')->get()));
    content()->create('registry-root/level-1/level-2', serializers()->yaml()->decode(filesystem()->file(ROOT_DIR . '/tests/fixtures/content/registry-root/level-1/level-2/content.yaml')->get()));

    $data = content()->fetch('registry-root');

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
