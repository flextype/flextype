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

test('entries expression', function () {
    entries()->create('foo', ['title' => 'Foo']);
    entries()->create('entries', ['test' => '[[ entries().fetch("foo").get("title") ]]', 
                                  'test2' => '(type:bool) [[ entries().has("foo") ]]',
                                  'test3' => '(type:bool) [[ entries().has("bar") ]]']);
    expect(entries()->fetch('entries')['test'])->toBe('Foo');
    expect(entries()->fetch('entries')['test2'])->toBe(true);
    expect(entries()->fetch('entries')['test3'])->toBe(false);
});