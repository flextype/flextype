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

test('field expression', function () {
    
    entries()->create('field', [
        'title' => 'Title',
        '_' => [
            'foo' => 'Foo',
            'level2' => [
                'value' => 'Bar',
            ]
        ], 

        // Get
        'test-get-1' => '[[ field("title") ]]',
        'test-get-2' => '[[ title ]]',
        'test-get-3' => '[[ _.foo ]]',
        'test-get-4' => "[[ fields().get('_.foo') ]]",
        'test-get-5' => "[[ fields().get('_.bar', 'Default') ]]",

        // Set add Get
        'test-set-1' => '[% fields().set("qwerty", "Qwerty") %]',
        'test-set-2' => '[[ fields().get("qwerty") ]] [[qwerty ]] [[ qwerty]] [[qwerty]] [[ qwerty ]]',

        // Delete
        'test-delete-1' => '[% fields().delete("qwerty") %]',
    ]);

    expect(entries()->fetch('field')['test-get-1'])->toBe('Title');
    expect(entries()->fetch('field')['test-get-2'])->toBe('Title');
    expect(entries()->fetch('field')['test-get-3'])->toBe('Foo');
    expect(entries()->fetch('field')['test-get-4'])->toBe('Foo');
    expect(entries()->fetch('field')['test-get-5'])->toBe('Default');
    expect(entries()->fetch('field')['test-set-1'])->toBe('');
    expect(entries()->fetch('field')['test-set-2'])->toBe('Qwerty Qwerty Qwerty Qwerty Qwerty');
    expect(entries()->fetch('field')['test-delete-1'])->toBe('');
});