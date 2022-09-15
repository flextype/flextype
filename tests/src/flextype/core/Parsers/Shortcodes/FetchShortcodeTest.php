<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\fetch;
use function Flextype\entries;
use function Flextype\registry;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('fetch shortcode', function () {
    
    // 1.
    $this->assertTrue(entries()->create('data', ['title' => 'Foo', 'category' => 'foo']));
    $this->assertTrue(entries()->create('foo', ['test1' => "(fetch:data field:'title')", 
                                                'test2' => '(fetch:data)', 
                                                'test3' => "(fetch:data field:'foo' default:'FooDefault')"]));
    
    $foo = entries()->fetch('foo');

    expect($foo['test1'])->toBe('Foo');
    expect($foo['test2'])->toBeJson();
    expect($foo['test3'])->toBe('FooDefault');

    // 2.
    $this->assertTrue(entries()->create('bar', ['test1' => "(fetch:'(const:FLEXTYPE_ROOT_DIR)/src/flextype/flextype.yaml' field:'name')", 
                                                'test2' => "(fetch:'(const:FLEXTYPE_ROOT_DIR)/src/flextype/flextype.yaml')", 
                                                'test3' => "(fetch:'(const:FLEXTYPE_ROOT_DIR)/src/flextype/flextype.yaml' field:boo default:Boo)"]));

    $bar = entries()->fetch('bar');

    expect($bar['test1'])->toBe('Flextype');
    expect($bar['test2'])->toBeJson();
    expect($bar['test3'])->toBe('Boo');

    // 3. @TODO test for fetch data from the urls...
});

test('entries shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.fetch.enabled', false);
    expect(entries()->create('foo', ['test' => ""]))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.fetch.enabled', true);
});