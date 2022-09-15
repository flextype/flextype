<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('field shortcode', function () {
    expect(entries()->create('foo', [
        'title' => 'Title',
        
        // get
        'test-get-1' => '(field:title)',
        'test-get-2' => '(field get:title)',
        'test-get-3' => '(field get:foo default:Foo)',
        'test-get-4' => '(field get:foo)Foo(/field)',

        // set
        'test-set-1' => '(field set:bar1 value:Bar1)(field:bar1)',
        'test-set-2' => '(field set:bar2)Bar2(/field)(field:bar2)',
        'test-set-3' => '(field set:level1.level2.level3)Multilevel(/field)(field:level1.level2.level3)',
        'test-set-4' => '(field set:_.foo)Foo(/field)(field:_.foo)',

        // unset
        'test-unset-1' => '(field unset:bar1)(field:bar1)',

        // delete
        'test-delete-1' => '(field delete:bar1)(field:bar1)',
    ]))->toBeTrue();
    
    $foo = entries()->fetch('foo');

    expect($foo['test-get-1'])->toBe('Title');
    expect($foo['test-get-2'])->toBe('Title');
    expect($foo['test-get-3'])->toBe('Foo');
    expect($foo['test-get-4'])->toBe('Foo');
    expect($foo['test-set-1'])->toBe('Bar1');
    expect($foo['test-set-2'])->toBe('Bar2');
    expect($foo['test-set-3'])->toBe('Multilevel');
    expect($foo['test-set-4'])->toBe('Foo');
    expect($foo['test-unset-1'])->toBe('');
    expect($foo['test-delete-1'])->toBe('');
});

test('field shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.field.enabled', false);
    expect(entries()->create('foo', ['test' => '(field:id)']))->toBeTrue();
    expect(entries()->fetch('foo')['test'])->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.field.enabled', true);
});