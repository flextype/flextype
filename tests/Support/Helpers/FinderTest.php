<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
    flextype('entries')->create('foo', []);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test find() method', function () {
    $this->assertInstanceOf(Finder::class, find());
});

test('test find_filter() method', function () {
    $this->assertTrue(find_filter(PATH['project'])->hasResults());
    $this->assertTrue(find_filter(PATH['project'], [])->hasResults());
    $this->assertTrue(find_filter(PATH['project'], [], 'files')->hasResults());
    $this->assertTrue(find_filter(PATH['project'], [], 'directories')->hasResults());
});
