<?php

use Flextype\Component\Filesystem\Filesystem;

use function Glowy\Filesystem\filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('collection expression', function () {
    entries()->create('collection', ['test-1' => '[[ collection({"foo": "Foo"}).get("foo") ]]',
                                     'test-2' => '[[ collectionFromString("a,b", ",").offsetGet(0) ]]',
                                     'test-3' => '[[ collectionFromJson("{\"foo\": \"Foo\"}").get("foo") ]]',
                                     'test-4' => '[[ collectionFromQueryString("foo=Foo").get("foo") ]]',
                                     'test-5' => '[[ collectionWithRange(0,10,1).offsetGet(10) ]]']);

    expect(entries('collection')->fetch('collection')['test-1'])->toBe('Foo');
    expect(entries('collection')->fetch('collection')['test-2'])->toBe('a');
    expect(entries('collection')->fetch('collection')['test-3'])->toBe('Foo');
    expect(entries('collection')->fetch('collection')['test-4'])->toBe('Foo');
    expect(entries('collection')->fetch('collection')['test-5'])->toBe('10');
});