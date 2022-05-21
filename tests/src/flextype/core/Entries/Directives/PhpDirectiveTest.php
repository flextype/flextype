<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('php directive', function () {
    entries()->create('type-php', ['title' => '@php echo "Foo";']);

    $this->assertEquals('Foo', entries()->fetch('type-php')['title']);
});