<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('types directive', function () {
    entries()->create('type-int', ['foo' => '@type[int] 100']);
    entries()->create('type-integer', ['foo' => '@type[integer] 100']);
    entries()->create('type-bool', ['foo' => '@type[bool] true']);
    entries()->create('type-boolean', ['foo' => '@type[boolean] false']);
    entries()->create('type-float', ['foo' => '@type[float] 1.5']);
    entries()->create('type-array', ['foo' => '@type[array] foo=bar']);
    entries()->create('type-array-2', ['foo' => '@type[array] [1,2,3,4,5]']);
    entries()->create('type-array-3', ['foo' => '@type[array] {"foo": "Foo"}']);
    entries()->create('type-array-4', ['foo' => '@type[array] foo']);
    entries()->create('type-collection', ['foo' => '@type[collection] foo']);
    entries()->create('type-collection-2', ['foo' => '@type[collection] {"foo": "Foo"}']);
    entries()->create('type-null', ['foo' => '@type[null] foo']);
    entries()->create('type-string', ['foo' => '@type[string] foo']);
    entries()->create('type-json', ['foo' => '@type[json] foo=Foo']);
    entries()->create('type-json-2', ['foo' => '@type[json] {"foo": "Foo"}']);
    entries()->create('type-json-3', ['foo' => '@type[json] [1,2,3,4,5]']);

    $this->assertEquals(100, entries()->fetch('type-int')['foo']);
    $this->assertEquals(100, entries()->fetch('type-integer')['foo']);
    $this->assertEquals(true, entries()->fetch('type-bool')['foo']);
    $this->assertEquals(false, entries()->fetch('type-boolean')['foo']);
    $this->assertEquals(1.5, entries()->fetch('type-float')['foo']);
    $this->assertEquals(['foo' => 'bar'], entries()->fetch('type-array')['foo']);
    $this->assertEquals([1,2,3,4,5], entries()->fetch('type-array-2')['foo']);
    $this->assertEquals(['foo' => 'Foo'], entries()->fetch('type-array-3')['foo']);
    $this->assertEquals(['foo' => ''], entries()->fetch('type-array-4')['foo']);
    $this->assertEquals(['foo' => ''], entries()->fetch('type-collection')['foo']->toArray());
    $this->assertEquals(['foo' => 'Foo'], entries()->fetch('type-collection-2')['foo']->toArray());
    $this->assertEquals(null, entries()->fetch('type-null')['foo']);
    $this->assertEquals('foo', entries()->fetch('type-string')['foo']);
    $this->assertEquals('{"foo":"Foo"}', entries()->fetch('type-json')['foo']);
    $this->assertEquals('{"foo": "Foo"}', entries()->fetch('type-json-2')['foo']);
    $this->assertEquals('[1,2,3,4,5]', entries()->fetch('type-json-3')['foo']);
});

test('types directive disabled', function () {
    registry()->set('flextype.settings.entries.directives.types.enabled', false);
    entries()->create('field', ['foo' => '@type[int] 100']);
    $this->assertEquals('@type[int] 100', entries()->fetch('field')['foo']);
    registry()->set('flextype.settings.entries.directives.types.enabled', true);
});