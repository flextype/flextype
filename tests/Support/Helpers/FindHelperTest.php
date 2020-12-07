<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create(0755, true);
    flextype('entries')->create('foo', []);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test find() method', function () {
    $this->assertTrue(find(PATH['project'] . '/entries')->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries', [])->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries', [], 'files')->hasResults());
    $this->assertTrue(find(PATH['project'], [], 'directories')->hasResults());
});
