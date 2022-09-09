<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;
use function Flextype\parsers;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('var shortcode', function () {
    expect(entries()->create('foo', [
        'title' => 'Title',
    
        // set
        'test-set-1' => '(var set:bar1 value:Bar1)(var:bar1)',
        'test-set-2' => '(var set:bar2)Bar2(/var)(var:bar2)',
        'test-set-3' => '(var set:level1.level2.level3)Multilevel(/var)(var:level1.level2.level3)',
        'test-set-4' => '(var set:_.foo)Foo(/var)(var:_.foo)',

        // get
        'test-get-1' => '(var:bar1)',
        'test-get-3' => '(var get:foo default:Foo)',
        'test-get-4' => '(var get:foo)Foo(/var)',
        
        // unset
        'test-unset-1' => '(var unset:bar1)(var:bar1)',

        // delete
        'test-delete-1' => '(var delete:bar1)(var:bar1)',
    ]))->toBeTrue();
    
    $foo = entries()->fetch('foo');
    
    expect($foo['test-get-3'])->toBe('Foo');
    expect($foo['test-get-4'])->toBe('Foo');
    expect($foo['test-set-1'])->toBe('Bar1');
    expect($foo['test-set-2'])->toBe('Bar2');
    expect($foo['test-set-3'])->toBe('Multilevel');
    expect($foo['test-set-4'])->toBe('Foo');
    expect($foo['test-unset-1'])->toBe('');
    expect($foo['test-delete-1'])->toBe('');
});

test('var shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.var.enabled', false);
    expect(parsers()->shortcodes()->parse("(var set:bar value:Bar)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.var.enabled', true);
});