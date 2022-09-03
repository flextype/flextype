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

test('var + field expression', function () {
    entries()->create('var', [
        'title' => 'Foo',
        'vars' => [
            'foo' => 'Foo'
        ], 
        'test1' => '[[ var("foo") ]]',
        'test2' => "[[ get('vars.foo', 'Foo') ]]",
        'test3' => '[[ vars.foo ]]',
        'test4' => '[[ set("bar", "Bar") ]][[ bar ]]',
        'test5' => '[[ unset("bar") ]]',
        'test6' => '[[ delete("bar") ]]',
        'test7' => '[[ title ]] [[ get("title") ]]',
    ]);

    expect(entries()->fetch('var')['test1'])->toBe('Foo');
    expect(entries()->fetch('var')['test2'])->toBe('Foo');
    expect(entries()->fetch('var')['test3'])->toBe('Foo');
    expect(entries()->fetch('var')['test4'])->toBe('Bar');
    expect(entries()->fetch('var')['test5'])->toBe('');
    expect(entries()->fetch('var')['test6'])->toBe('');
    expect(entries()->fetch('var')['test7'])->toBe('Foo Foo');
});