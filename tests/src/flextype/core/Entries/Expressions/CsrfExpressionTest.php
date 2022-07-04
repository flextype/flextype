<?php

use Flextype\Component\Filesystem\Filesystem;

use function Glowy\Filesystem\filesystem;
use function Glowy\Strings\strings;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('strings expression', function () {
    entries()->create('csrf', ['test' => '[[ csrf() ]]']);
    expect(strings(entries()->fetch('csrf')['test'])->length())->toBe(178);
});