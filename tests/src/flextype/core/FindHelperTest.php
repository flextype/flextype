<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/content')->create(0755, true);
    content()->create('foo', []);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/content')->delete();
});

test('test find() method', function () {
    $this->assertTrue(find(PATH['project'] . '/entries/content')->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries/content', [])->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries/content', [], 'files')->hasResults());
    $this->assertTrue(find(PATH['project'], [], 'directories')->hasResults());
});
