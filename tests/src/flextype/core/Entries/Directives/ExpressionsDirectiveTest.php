<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('expressions directive', function () {
    entries()->create('expressions', ['test' => '[[ 1+1 ]]']);
    $this->assertEquals(2, entries()->fetch('expressions')['test']);
});

test('expressions directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.expressions.enabled', false);
    entries()->create('expressions', ['test' => '[[ 1+1 ]]']);
    expect(entries()->fetch('expressions')['test'])->toBe('[[ 1+1 ]]');
    registry()->set('flextype.settings.entries.directives.expressions.enabled', true);
});

test('expressions directive disabled globaly', function () {
    registry()->set('flextype.settings.entries.directives.expressions.enabled_globally', false);
    entries()->create('expressions', ['test' => '[[ 1+1 ]]']);
    expect(entries()->fetch('expressions')['test'])->toBe('[[ 1+1 ]]');
    registry()->set('flextype.settings.entries.directives.expressions.enabled_globally', true);
});
