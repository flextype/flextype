<?php

declare(strict_types=1);

use function Flextype\serializers;

test('encode', function () {
    $this->assertEquals('{"title":"Foo","content":"Bar"}',
                        serializers()->json()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Bar'],
                        serializers()->json()
                            ->decode('{"title":"Foo","content":"Bar"}'));
});

test('get cache ID', function () {
    $string = '{"title":"Foo","content":"Bar"}';
    $cache_id = serializers()->json()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
