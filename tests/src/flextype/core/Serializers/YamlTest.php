<?php

declare(strict_types=1);

test('encode', function () {
    $this->assertEquals("title: Foo\ncontent: Bar\n",
                        serializers()->yaml()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Bar']));
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Bar'],
                        serializers()->yaml()
                            ->decode("title: Foo\ncontent: Bar"));

    $this->assertEquals(['title' => 'Foo',
                        'content' => 'Bar'],
                        serializers()->yaml()
                            ->decode("{\"title\": \"Foo\", \"content\": \"Bar\"}"));
});

test('get cache ID', function () {
    $string = "title:Foo\ncontent:Bar";
    $cache_id = serializers()->yaml()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
