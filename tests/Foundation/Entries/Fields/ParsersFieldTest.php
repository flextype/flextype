<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test ParsersField', function () {
    entries()->create('foo', ['entries' => '# Foo', 'parsers' => ['markdown' => ['enabled' => true, 'fields' => ['entries']]]]);
    $this->assertEquals(trim('<h1>Foo</h1>'), trim(entries()->fetch('foo')['entries']));

    entries()->create('bar', ['entries' => '[registry_get name="Bar" default="Zed"]', 'parsers' => ['shortcode' => ['enabled' => true, 'fields' => ['entries']]]]);
    $this->assertEquals('Zed', entries()->fetch('bar')['entries']);
});
