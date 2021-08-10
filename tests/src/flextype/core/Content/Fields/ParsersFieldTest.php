<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test ParsersField', function () {
    content()->create('bar', ['content' => '[registry_get name="Bar" default="Zed"]', 'parsers' => ['shortcodes' => ['enabled' => true, 'fields' => ['content']]]]);
    $this->assertEquals('Zed', content()->fetch('bar')['content']);
});
