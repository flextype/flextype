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