<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals('{"title":"Foo","entries":"Bar"}',
                        serializers()->json()
                            ->encode(['title' => 'Foo',
                                      'entries' => 'Bar']));
});

test('test decode() method', function () {
    $this->assertEquals(['title' => 'Foo',
                         'entries' => 'Bar'],
                        serializers()->json()
                            ->decode('{"title":"Foo","entries":"Bar"}'));
});

test('test getCacheID() method', function () {
    $string = '{"title":"Foo","entries":"Bar"}';
    $cache_id = serializers()->json()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
