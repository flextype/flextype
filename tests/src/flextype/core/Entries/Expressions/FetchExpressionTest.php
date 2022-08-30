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

test('fetch expression', function () {

    // 1.
    $this->assertTrue(entries()->create('data', ['title' => 'Foo!', 'category' => 'foo']));
    $this->assertTrue(entries()->create('foo-test', ['test1' => "[[ fetch('data').get('title') ]]", 
                                                 'test2' => "[[ fetch('data').toJson() ]]", 
                                                 'test3' => "[[ fetch('data').get('foo', 'FooDefault') ]]"]));
    
    $foo = entries()->fetch('foo-test');

    expect($foo['test1'])->toBe('Foo!');
    expect($foo['test2'])->toBeJson();
    expect($foo['test3'])->toBe('FooDefault');

    // 2.
    $this->assertTrue(entries()->create('bar-test', ['test1' => "[[ fetch(const('FLEXTYPE_ROOT_DIR') ~ '/src/flextype/flextype.yaml').get('name') ]]", 
                                                     'test2' => "[[ fetch(const('FLEXTYPE_ROOT_DIR') ~ '/src/flextype/flextype.yaml').toJson() ]]", 
                                                     'test3' => "[[ fetch(const('FLEXTYPE_ROOT_DIR') ~ '/src/flextype/flextype.yaml').get('boo', 'Boo') ]]"]));

    $bar = entries()->fetch('bar-test');

    expect($bar['test1'])->toBe('Flextype');
    expect($bar['test2'])->toBeJson();
    expect($bar['test3'])->toBe('Boo');

    // 3. @TODO test for fetch data from the urls...
});