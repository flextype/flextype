<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test ParsersField', function () {
    flextype('entries')->create('foo', ['content' => '#Foo', 'parsers' => ['markdown' => ['enabled' => true, 'fields' => ['content']]]]);
    $this->assertEquals('<h1>Foo</h1>', flextype('entries')->fetchSingle('foo')['content']);

    flextype('entries')->create('bar', ['content' => '[registry_get name="Bar" default="Zed"]', 'parsers' => ['shortcode' => ['enabled' => true, 'fields' => ['content']]]]);
    $this->assertEquals('Zed', flextype('entries')->fetchSingle('bar')['content']);
});
