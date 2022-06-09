<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH_PROJECT . '/entries')->delete();
});

test('php directive', function () {
    entries()->create('type-php', ['title' => '@php echo "Foo";']);

    $this->assertEquals('Foo', entries()->fetch('type-php')['title']);
});

test('php directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.php.enabled', false);
    entries()->create('type-php', ['title' => '@php echo "Foo";']);
    $this->assertEquals('@php echo "Foo";', entries()->fetch('type-php')['title']);
    registry()->set('flextype.settings.entries.directives.php.enabled', true);
});