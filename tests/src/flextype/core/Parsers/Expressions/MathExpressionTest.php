<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;
use function Flextype\entries;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('ceil expression', function () {
    entries()->create('ceil', ['test' => '[[ ceil(42.1) ]]']);
    expect(entries()->fetch('ceil')['test'])->toBe('43');
});

test('floor expression', function () {
    entries()->create('floor', ['test' => '[[ floor(4.3) ]]']);
    expect(entries()->fetch('floor')['test'])->toBe('4');
});

test('min expression', function () {
    entries()->create('min', ['test' => '[[ min(2, 3, 1, 6, 7) ]]']);
    expect(entries()->fetch('min')['test'])->toBe('1');
});

test('max expression', function () {
    entries()->create('max', ['test' => '[[ max(2, 3, 1, 6, 7) ]]']);
    expect(entries()->fetch('max')['test'])->toBe('7');
});

test('abs expression', function () {
    entries()->create('abs', ['test' => '[[ abs(-4.2) ]]']);
    expect(entries()->fetch('abs')['test'])->toBe('4.2');
});

test('round expression', function () {
    entries()->create('round', ['test' => '[[ round(3.4) ]]']);
    expect(entries()->fetch('round')['test'])->toBe('3');
});