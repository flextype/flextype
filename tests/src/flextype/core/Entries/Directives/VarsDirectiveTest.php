<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('vars directive', function () {
    entries()->create('type-vars', ['vars' => ['foo' => 'Foo'], 'title' => '@var[foo]']);

    $this->assertEquals('Foo', entries()->fetch('type-vars')['title']);
});

test('vars directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.vars.enabled', false);
    entries()->create('type-vars', ['vars' => ['foo' => 'Foo'], 'title' => '@var[foo]']);
    $this->assertEquals('@var[foo]', entries()->fetch('type-vars')['title']);
    registry()->set('flextype.settings.entries.directives.vars.enabled', true);
});