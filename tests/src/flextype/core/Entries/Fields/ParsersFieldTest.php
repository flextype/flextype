<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('ParsersField', function () {
    entries()->create('bar-parsers', ['content' => '[registry_get name="Bar" default="Zed"]', 'parsers' => ['shortcodes' => ['enabled' => true, 'fields' => ['content']]]]);

    $this->assertEquals('Zed', entries()->fetch('bar-parsers')['content']);
});
