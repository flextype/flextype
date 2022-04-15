<?php

declare(strict_types=1);

test('encode', function () {
    $this->assertEquals('{"title":"Foo","content":"Bar"}',
                        serializers()->json5()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Bar'],
                        serializers()->json5()
                            ->decode('{"title":"Foo","content":"Bar"}'));
});

test('get cache ID', function () {
    $string = '{"title":"Foo","content":"Bar"}';
    $cache_id = serializers()->json5()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
