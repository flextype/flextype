<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test registry field', function () {
    flextype('entries')->create('registry-root', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/registry-root/entry.md')->get()));
    flextype('entries')->create('registry-root/level-1', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/registry-root/level-1/entry.md')->get()));
    flextype('entries')->create('registry-root/level-1/level-2', flextype('serializers')->frontmatter()->decode(filesystem()->file(ROOT_DIR . '/tests/Foundation/Entries/Fields/fixtures/entries/registry-root/level-1/level-2/entry.md')->get()));

    $data = flextype('entries')->fetch('registry-root');

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
