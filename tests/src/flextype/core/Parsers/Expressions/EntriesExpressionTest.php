<?php

use Flextype\Component\Filesystem\Filesystem;
use function Glowy\Filesystem\filesystem;
use function Flextype\entries;
use function Flextype\registry;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('entries expression', function () {
    // fetch
    entries()->create('foo', ['title' => 'Foo']);
    entries()->create('fetch', ['test' => '[[ entries().fetch("foo").get("title") ]]']);
    expect(entries()->fetch('fetch')['test'])->toBe('Foo');

    // has
    entries()->create('has', [
        'test1' => '(type:bool) [[ entries().has("foo") ]]',
        'test2' => '(type:bool) [[ entries().has("bar") ]]',
    ]);

    expect(entries()->fetch('has')['test1'])->toBe(true);
    expect(entries()->fetch('has')['test2'])->toBe(false);

    // delete
    entries()->create('delete', [
        'test' => '(type:bool) [[ entries().delete("foo") ]]',
    ]);

    registry()->set('flextype.settings.parsers.expressions.expressions.entries.delete.enabled', true);
    expect(entries()->fetch('delete')['test'])->toBeTrue();
    registry()->set('flextype.settings.parsers.expressions.expressions.entries.delete.enabled', false);

    // copy
    entries()->create('copy-foo');
    entries()->create('copy', [
        'test' => '(type:bool) [[ entries().copy("copy-foo", "copy-foo-2") ]]',
    ]);

    registry()->set('flextype.settings.parsers.expressions.expressions.entries.copy.enabled', true);
    expect(entries()->fetch('copy')['test'])->toBeTrue();
    registry()->set('flextype.settings.parsers.expressions.expressions.entries.copy.enabled', false);

    // move
    entries()->create('move-foo');
    entries()->create('move', [
        'test' => '(type:bool) [[ entries().move("move-foo", "move-foo-2") ]]',
    ]);

    registry()->set('flextype.settings.parsers.expressions.expressions.entries.move.enabled', true);
    expect(entries()->fetch('move')['test'])->toBeTrue();
    registry()->set('flextype.settings.parsers.expressions.expressions.entries.move.enabled', false);

    // update
    entries()->create('update-foo');
    entries()->create('update', [
        'test' => '(type:bool) [[ entries().update("update-foo", {"title": "Foo"}) ]]',
    ]);

    registry()->set('flextype.settings.parsers.expressions.expressions.entries.update.enabled', true);
    expect(entries()->fetch('update')['test'])->toBeTrue();
    expect(entries()->fetch('update-foo')['title'])->toBe('Foo');
    registry()->set('flextype.settings.parsers.expressions.expressions.entries.update.enabled', false);
});