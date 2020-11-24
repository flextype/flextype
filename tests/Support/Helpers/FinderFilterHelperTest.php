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

test('test find_filter() method', function () {
    $this->assertTrue(find_filter(PATH['project'] . '/entries')->hasResults());
    $this->assertTrue(find_filter(PATH['project'] . '/entries', [])->hasResults());
    $this->assertTrue(find_filter(PATH['project'] . '/entries', [], 'files')->hasResults());
    $this->assertTrue(find_filter(PATH['project'], [], 'directories')->hasResults());
});
